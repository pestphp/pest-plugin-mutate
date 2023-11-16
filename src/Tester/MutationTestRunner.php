<?php

declare(strict_types=1);

namespace Pest\Mutate\Tester;

use Pest\Mutate\Contracts\MutationTestRunner as MutationTestRunnerContract;
use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\Event\Facade;
use Pest\Mutate\Mutation;
use Pest\Mutate\MutationSuite;
use Pest\Mutate\Plugins\Mutate;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Support\Configuration\Configuration;
use Pest\Mutate\Support\MutationGenerator;
use Pest\Support\Container;
use Pest\Support\Coverage;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MutationTestRunner implements MutationTestRunnerContract
{
    private bool $enabled = false;

    private bool $codeCoverageRequested = false;

    private bool $stop = false;

    /**
     * @var array<int, string>
     */
    private array $originalArguments;

    public static function fake(): MutationTestRunnerFake
    {
        $fake = new MutationTestRunnerFake();

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

    public function run(): void
    {
        $start = microtime(true);

        if (! file_exists($reportPath = Coverage::getPath())) {
            // TODO: maybe we can run without a coverage report, but it is really in performant
            Container::getInstance()->get(Printer::class)->reportError('No coverage report found, aborting mutation testing.'); // @phpstan-ignore-line
            exit(1);
        }

        $mutationSuite = MutationSuite::instance();

        Facade::instance()->emitter()->startMutationGeneration($mutationSuite);

        /** @var CodeCoverage $codeCoverage */
        $codeCoverage = require $reportPath;
        unlink($reportPath);
        $coveredLines = array_map(fn (array $lines): array => array_filter($lines, fn (array $tests): bool => $tests !== []), $codeCoverage->getData()->lineCoverage());
        $coveredLines = array_filter($coveredLines, fn (array $lines): bool => $lines !== []);

        $files = $this->getFiles($this->getConfiguration()->paths);

        /** @var MutationGenerator $generator */
        $generator = Container::getInstance()->get(MutationGenerator::class);
        foreach ($files as $file) {
            if ($this->getConfiguration()->coveredOnly && ! isset($coveredLines[$file->getRealPath()])) {
                continue;
            }

            foreach ($generator->generate(
                file: $file,
                mutators: $this->getConfiguration()->mutators,
                linesToMutate: $this->getConfiguration()->coveredOnly ? array_keys($coveredLines[$file->getRealPath()]) : [],
                classesToMutate: $this->getConfiguration()->classes,
            ) as $mutation) {
                $mutationSuite->repository->add($mutation);
            }
        }

        Facade::instance()->emitter()->finishMutationGeneration($mutationSuite);

        Facade::instance()->emitter()->startMutationSuite($mutationSuite);

        // run tests for each mutation
        foreach ($mutationSuite->repository->all() as $testCollection) {
            Facade::instance()->emitter()->startTestCollection($testCollection);

            foreach ($testCollection->tests() as $test) {
                if ($this->stop) {
                    break 2;
                }

                $test->run($coveredLines, $this->getConfiguration(), $this->originalArguments);
            }
        }

        Facade::instance()->emitter()->finishMutationSuite($mutationSuite);

        exit(0); // TODO: exit with error on failure
    }

    /**
     * @param  array<array-key, string>  $paths
     */
    private function getFiles(array $paths): Finder
    {
        $dirs = [];
        $filePaths = [];
        foreach ($paths as $path) {
            if (! str_starts_with($path, DIRECTORY_SEPARATOR)) {
                $path = getcwd().DIRECTORY_SEPARATOR.$path;
            }
            if (is_dir($path)) {
                $dirs[] = $path;
            } elseif (is_file($path)) {
                $file = new \SplFileInfo($path);
                $filePaths[] = new SplFileInfo($file->getPathname(), '', $file->getFilename());
            }
        }

        return Finder::create()
            ->in($dirs)
            ->name('*.php')
//            ->notPath($options->ignoring)
            ->append($filePaths)
            ->files();
    }

    private function getConfiguration(): Configuration
    {
        return Container::getInstance()->get(ConfigurationRepository::class)->mergedConfiguration(); // @phpstan-ignore-line
    }
}
