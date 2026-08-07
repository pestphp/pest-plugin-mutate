<?php

declare(strict_types=1);

namespace Pest\Mutate\Tester;

use Composer\InstalledVersions;
use Pest\Mutate\Contracts\MutationTestRunner as MutationTestRunnerContract;
use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\Event\Facade;
use Pest\Mutate\MutationSuite;
use Pest\Mutate\MutationTest;
use Pest\Mutate\Plugins\Mutate;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Repositories\TelemetryRepository;
use Pest\Mutate\Support\Configuration\Configuration;
use Pest\Mutate\Support\FileFinder;
use Pest\Mutate\Support\MutationGenerator;
use Pest\Plugins\Shard;
use Pest\Support\Container;
use Pest\Support\Coverage;
use Pest\TestSuite;
use Psr\SimpleCache\CacheInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;

class MutationTestRunner implements MutationTestRunnerContract
{
    private bool $enabled = false;

    private bool $codeCoverageRequested = false;

    private bool $stop = false;

    /**
     * @var array<int, string>
     */
    private array $originalArguments;

    /**
     * @var array<int, ?MutationTest>
     */
    private array $runningTests;

    private float $startTime;

    public static function fake(): MutationTestRunnerFake
    {
        $fake = new MutationTestRunnerFake;

        Container::getInstance()->add(MutationTestRunnerContract::class, $fake);

        return $fake;
    }

    public function stopExecution(): void
    {
        $this->stop = true;
    }

    /**
     * @param  array<int, string>  $arguments
     */
    public function setOriginalArguments(array $arguments): void
    {
        $this->originalArguments = $arguments;
    }

    public function enable(): void
    {
        if (getenv(Mutate::ENV_MUTATION_TESTING) !== false) {
            return;
        }

        $this->enabled = true;
    }

    public function isEnabled(): bool
    {
        if (str_starts_with((string) $this->enabled, ConfigurationRepository::FAKE)) {
            return false;
        }

        return $this->enabled;
    }

    public function doNotDisableCodeCoverage(): void
    {
        $this->codeCoverageRequested = true;
    }

    public function isCodeCoverageRequested(): bool
    {
        return $this->codeCoverageRequested;
    }

    public function run(): int
    {
        Container::getInstance()->get(TelemetryRepository::class)->initialTestSuiteDuration( // @phpstan-ignore-line
            microtime(true) - $this->startTime
        );

        if (! Coverage::isAvailable() || ! file_exists($reportPath = Coverage::getPath())) {
            Container::getInstance()->get(Printer::class)->reportError('No coverage report found, aborting mutation testing.'); // @phpstan-ignore-line

            return 1;
        }

        $this->clearCacheIfPluginVersionChanged();

        $mutationSuite = MutationSuite::instance();

        Facade::instance()->emitter()->startMutationGeneration($mutationSuite);

        /** @var CodeCoverage|array{basePath: string, codeCoverage: ProcessedCodeCoverageData} $loadedCoverage */
        $loadedCoverage = require $reportPath;

        unlink($reportPath);

        if ($loadedCoverage instanceof CodeCoverage) {
            $coverageData = $loadedCoverage->getData();
        } else {
            // since phpunit/php-code-coverage 14, `--coverage-php` writes an array instead of a
            // serialized CodeCoverage, and its file keys are relative to `basePath`
            $coverageData = $loadedCoverage['codeCoverage'];
            $basePath = $loadedCoverage['basePath'];

            if ($basePath !== '') {
                foreach ($coverageData->coveredFiles() as $relativePath) {
                    $coverageData->renameFile($relativePath, $basePath.DIRECTORY_SEPARATOR.$relativePath);
                }
            }
        }

        $coveredLines = $this->coveredLines($coverageData);

        $files = FileFinder::files($this->getConfiguration()->paths, $this->getConfiguration()->pathsToIgnore);

        /** @var MutationGenerator $generator */
        $generator = Container::getInstance()->get(MutationGenerator::class);

        $shardFiles = $this->shardFiles();

        foreach ($files as $file) {
            if ($shardFiles !== null && ! isset($shardFiles[$file->getRealPath()])) {
                continue;
            }

            $linesToMutate = [];

            if ($this->getConfiguration()->coveredOnly) {

                if (! isset($coveredLines[$file->getRealPath()])) {
                    continue;
                }

                $linesToMutate = array_keys($coveredLines[$file->getRealPath()]);
            }

            foreach ($generator->generate(
                file: $file,
                mutators: $this->getConfiguration()->mutators,
                linesToMutate: $linesToMutate,
                classesToMutate: $this->getConfiguration()->everything ? [] : $this->getConfiguration()->classes,
            ) as $mutation) {
                if ($this->getConfiguration()->mutationId !== null && $mutation->id !== $this->getConfiguration()->mutationId) {
                    continue;
                }

                $mutationSuite->repository->add($mutation);
            }
        }

        Facade::instance()->emitter()->finishMutationGeneration($mutationSuite);

        if ($this->getConfiguration()->retry) {
            $mutationSuite->repository->sortByEscapedFirst();
        }

        Facade::instance()->emitter()->startMutationSuite($mutationSuite);

        if ($this->getConfiguration()->parallel) {
            $this->runTestsInParallel(
                mutationSuite: $mutationSuite,
                coveredLines: $coveredLines,
                processes: $this->getConfiguration()->processes,
            );
        } else {
            $this->runTests(
                mutationSuite: $mutationSuite,
                coveredLines: $coveredLines,
            );
        }

        $mutationSuite->repository->saveResults();

        $this->writeJsonLog($mutationSuite);

        // A suite cut short by --bail or --stop-on-* holds partial durations, which would
        // make for a badly balanced shards file.
        if (! $this->stop) {
            Shard::useTimings($mutationSuite->repository->units(TestSuite::getInstance()->rootPath));
        }

        Facade::instance()->emitter()->finishMutationSuite($mutationSuite);

        return $this->isMinScoreIsReached($mutationSuite) ? 0 : 1;
    }

    /**
     * Returns the tests covering each line, as `[file => [line => [testId, ...]]]`.
     *
     * @return array<string, array<int, array<int, string>>>
     */
    private function coveredLines(ProcessedCodeCoverageData $coverageData): array
    {
        /** @var array<int, string> $testIds */
        $testIds = method_exists($coverageData, 'testIds') ? $coverageData->testIds() : []; // @phpstan-ignore function.alreadyNarrowedType

        $coveredLines = [];

        foreach ($coverageData->lineCoverage() as $file => $lines) {
            foreach ($lines as $line => $tests) {
                $ids = $this->testIdsCoveringLine($tests ?? [], $testIds);

                if ($ids === []) {
                    continue;
                }

                $coveredLines[$file][$line] = $ids;
            }
        }

        return $coveredLines;
    }

    /**
     * Resolves the test ids a single line holds.
     *
     * Up to phpunit/php-code-coverage 14.2 a line held the test ids themselves. Since
     * 14.3 it holds hit counts, keyed by an index into the coverage data's test ids.
     *
     * @param  array<int|string, mixed>  $tests
     * @param  array<int, string>  $testIds
     * @return array<int, string>
     */
    private function testIdsCoveringLine(array $tests, array $testIds): array
    {
        $ids = [];

        foreach ($tests as $index => $test) {
            if (is_string($test)) {
                $ids[] = $test;

                continue;
            }

            if (is_int($index) && isset($testIds[$index])) {
                $ids[] = $testIds[$index];
            }
        }

        return $ids;
    }

    /**
     * Writes this run's results, so that sharded runs can be added up afterwards.
     *
     * Shards own disjoint sets of mutations, so summing the counters of every shard
     * reproduces the score of a single unsharded run exactly.
     */
    private function writeJsonLog(MutationSuite $mutationSuite): void
    {
        $path = $this->getConfiguration()->logJson;

        if ($path === null) {
            return;
        }

        $repository = $mutationSuite->repository;

        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, json_encode([
            'tested' => $repository->tested(),
            'untested' => $repository->untested(),
            'timeout' => $repository->timedOut(),
            'uncovered' => $repository->uncovered(),
            'not_run' => $repository->notRun(),
            'total' => $repository->total(),
            'score' => round($repository->score(), 2),
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n");
    }

    /**
     * Returns the absolute paths of the mutated files this shard owns, or null when the
     * run is not sharded.
     *
     * The restriction is applied here rather than through `--path` or `--class`, because
     * passing either of those lifts the `__pest_mutate_only` group and makes every shard
     * run the whole test suite instead of only the tests declaring `covers()`.
     *
     * @return array<string, true>|null
     */
    private function shardFiles(): ?array
    {
        $units = Shard::selectedUnits();

        if ($units === []) {
            return null;
        }

        $root = rtrim(TestSuite::getInstance()->rootPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        $files = [];

        foreach ($units as $unit) {
            $path = realpath(str_starts_with($unit, DIRECTORY_SEPARATOR) ? $unit : $root.$unit);

            if ($path !== false) {
                $files[$path] = true;
            }
        }

        return $files;
    }

    private function getConfiguration(): Configuration
    {
        return Container::getInstance()->get(ConfigurationRepository::class)->mergedConfiguration(); // @phpstan-ignore-line
    }

    private function isMinScoreIsReached(MutationSuite $mutationSuite): bool
    {
        /** @var ConfigurationRepository $configurationRepository */
        $configurationRepository = Container::getInstance()->get(ConfigurationRepository::class);

        $configuration = $configurationRepository->mergedConfiguration();

        $minScore = $configuration->minScore;

        if ($minScore === null) {
            return true;
        }

        if ($mutationSuite->repository->count() === 0 && $configuration->ignoreMinScoreOnZeroMutations) {
            return true;
        }

        $score = $mutationSuite->score();
        if ($score >= $minScore) {
            return true;
        }

        /** @var Printer $printer */
        $printer = Container::getInstance()->get(Printer::class);

        $printer->reportScoreNotReached($score, $minScore);

        return false;
    }

    /**
     * @param  array<string, array<int, array<int, string>>>  $coveredLines
     */
    private function runTestsInParallel(MutationSuite $mutationSuite, array $coveredLines, int $processes): void
    {
        $tests = [];
        foreach ($mutationSuite->repository->all() as $testCollection) {
            foreach ($testCollection->tests() as $test) {
                $tests[] = $test;
            }
        }

        $this->runningTests = array_fill(1, $processes, null);

        foreach ($tests as $test) {
            if ($this->stop) {
                break;
            }

            while (count(array_filter($this->runningTests, fn (?MutationTest $process): bool => $process instanceof MutationTest)) >= $processes) {
                if ($this->checkRunningTestsHaveFinished()) {
                    continue;
                }

                usleep(1000); // @pest-arch-ignore-line
            }

            $processId = (int) array_key_first(array_filter($this->runningTests, fn (?MutationTest $process): bool => ! $process instanceof MutationTest));
            if ($test->start($coveredLines, $this->getConfiguration(), $this->originalArguments, $processId)) {
                $this->runningTests[$processId] = $test;
            }
        }

        while (! $this->stop && array_filter($this->runningTests, fn (?MutationTest $process): bool => $process instanceof MutationTest) !== []) {
            $this->checkRunningTestsHaveFinished();
        }
    }

    /**
     * @param  array<string, array<int, array<int, string>>>  $coveredLines
     */
    private function runTests(MutationSuite $mutationSuite, array $coveredLines): void
    {
        foreach ($mutationSuite->repository->all() as $testCollection) {
            Facade::instance()->emitter()->startTestCollection($testCollection);

            foreach ($testCollection->tests() as $test) {
                if ($this->stop) {
                    break 2;
                }

                if ($test->start($coveredLines, $this->getConfiguration(), $this->originalArguments)) {
                    while (! $test->hasFinished()) {
                        usleep(1000);
                    }
                }
            }
        }
    }

    private function checkRunningTestsHaveFinished(): bool
    {
        foreach ($this->runningTests as $index => $runningTest) {
            if (! $runningTest instanceof MutationTest) {
                continue;
            }

            if ($runningTest->hasFinished()) {
                $this->runningTests[$index] = null;

                return true;
            }
        }

        return false;
    }

    private function clearCacheIfPluginVersionChanged(): void
    {
        $cache = Container::getInstance()->get(CacheInterface::class);

        $pluginVersion = InstalledVersions::getVersion('pestphp/pest-plugin-mutate');

        /** @var ?string $previousVersion */
        $previousVersion = $cache->get('mutation-plugin-version'); // @phpstan-ignore-line

        if ($previousVersion === null || $previousVersion !== $pluginVersion) {
            $cache->clear(); // @phpstan-ignore-line
        }

        $cache->set('mutation-plugin-version', $pluginVersion); // @phpstan-ignore-line
    }

    public function setStartTime(float $startTime): void
    {
        $this->startTime = $startTime;
    }
}
