<?php

declare(strict_types=1);

namespace Pest\Mutate\Tester;

use Pest\Exceptions\ShouldNotHappen;
use Pest\Mutate\Contracts\MutationTestRunner as MutationTestRunnerContract;
use Pest\Mutate\Factories\ProfileFactory;
use Pest\Mutate\Mutation;
use Pest\Mutate\MutationSuite;
use Pest\Mutate\Plugins\Mutate;
use Pest\Mutate\Profile;
use Pest\Mutate\Profiles;
use Pest\Mutate\Support\MutationGenerator;
use Pest\Mutate\Support\MutationTestResult;
use Pest\Support\Container;
use Pest\Support\Coverage;
use PHPUnit\TestRunner\TestResult\Facade;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

use function Termwind\render;
use function Termwind\renderUsing;

class MutationTestRunner implements MutationTestRunnerContract
{
    private ?string $enabledProfile = null;

    private bool $codeCoverageRequested = false;

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

    public function __construct(private readonly OutputInterface $output)
    {
    }

    /**
     * @param  array<int, string>  $arguments
     */
    public function setOriginalArguments(array $arguments): void
    {
        $this->originalArguments = $arguments;
    }

    public function enable(string $profile): void
    {
        if (getenv(Mutate::ENV_MUTATION_TESTING) !== false) {
            return;
        }

        $this->enabledProfile = $profile;
    }

    public function isEnabled(): bool
    {
        if (str_starts_with((string) $this->enabledProfile, Profile::FAKE)) {
            return false;
        }

        return $this->enabledProfile !== null;
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
        $this->assertInitialTestRunWasSuccessful();

        $start = microtime(true);

        if (! file_exists($reportPath = Coverage::getPath())) {
            // TODO: maybe we can run without a coverage report, but it is really in performant
            $this->output->writeln('No coverage report found, aborting mutation testing.');
            exit(1);
        }

        $mutationSuite = MutationSuite::instance();

        //        $this->output->writeln('Running mutation tests for profile: '.$this->enabledProfile);

        renderUsing($this->output);
        render('<div class="mx-2 mt-1">Generating mutations ...'.($this->enabledProfile !== 'default' ? (' (Profile: '.$this->enabledProfile.')') : '').'</div>');

        /** @var CodeCoverage $codeCoverage */
        $codeCoverage = require $reportPath;
        $coveredLines = array_map(fn (array $lines): array => array_filter($lines, fn (array $tests): bool => $tests !== []), $codeCoverage->getData()->lineCoverage());
        $coveredLines = array_filter($coveredLines, fn (array $lines): bool => $lines !== []);

        $files = $this->getFiles($this->getProfile()->paths);

        /** @var MutationGenerator $generator */
        $generator = Container::getInstance()->get(MutationGenerator::class);
        foreach ($files as $file) {
            if ($this->getProfile()->coveredOnly && ! isset($coveredLines[$file->getRealPath()])) {
                continue;
            }

            foreach ($generator->generate(
                file: $file,
                mutators: $this->getProfile()->mutators,
                linesToMutate: $this->getProfile()->coveredOnly ? array_keys($coveredLines[$file->getRealPath()]) : [],
                classesToMutate: $this->getProfile()->classes,
            ) as $mutation) {
                $mutationSuite->repository->add($mutation);
            }
        }

        $this->output->writeln([
            '  <fg=gray>'.$mutationSuite->repository->total().' Mutations for '.$mutationSuite->repository->count().' Files created</>',
            '',
        ]);

        renderUsing($this->output);
        render('<div class="m-2 my-1">Running mutation tests:</div>');

        $survivedCount = 0;
        $timeoutedCount = 0;
        $notCoveredCount = 0;

        $this->output->write('  ');

        // run tests for each mutation
        foreach ($mutationSuite->repository->all() as $mutations) {
            foreach ($mutations as $mutation) {
                /** @var string $tmpfname */
                $tmpfname = tempnam('/tmp', 'pest_mutation_');
                file_put_contents($tmpfname, $mutation->modifiedSource());

                // TODO: we should pass the tests to run in another way, maybe via cache, mutation or env variable
                $filters = [];
                foreach (range($mutation->originalNode->getStartLine(), $mutation->originalNode->getEndLine()) as $lineNumber) {
                    foreach ($coveredLines[$mutation->file->getRealPath()][$lineNumber] ?? [] as $test) {
                        preg_match('/\\\\([a-zA-Z0-9]*)::__pest_evaluable_([^#]*)"?/', (string) $test, $matches);
                        $filters[] = $matches[1].'::'.preg_replace(['/_([a-z])_/', '/([^_])_([^_])/', '/__/'], [' $1 ', '$1 $2', '_'], $matches[2]);
                    }
                }
                $filters = array_unique($filters);

                if ($filters === []) {
                    $mutation->updateResult(MutationTestResult::NotCovered);
                    $this->output->writeln('No tests found for mutation: '.$mutation->file->getRealPath().':'.$mutation->originalNode->getLine().' ('.$mutation->mutator::name().')');
                    $notCoveredCount++;

                    continue;
                }

                // TODO: filter arguments to remove unnecessary stuff (Teamcity, Coverage, etc.)
                $process = new Process(
                    command: [
                        ...$this->originalArguments,
                        '--bail',
                        '--filter="'.implode('|', $filters).'"',
                        $this->getProfile()->parallel ? '--parallel' : '',
                    ],
                    env: [
                        Mutate::ENV_MUTATION_TESTING => $mutation->file->getRealPath(),
                        Mutate::ENV_MUTATION_FILE => $tmpfname,
                    ],
                    timeout: $this->calculateTimeout(),
                );

                try {
                    $process->run();
                } catch (ProcessTimedOutException) {
                    $mutation->updateResult(MutationTestResult::Timeout);
                    $this->output->write('<fg=yellow;options=bold>t</>');
                    $timeoutedCount++;
                    $this->output->writeln('Mutant for '.$mutation->file->getRealPath().':'.$mutation->originalNode->getLine().' timed out. ('.$mutation->mutator.')');

                    continue;
                }

                if ($process->isSuccessful()) {
                    $mutation->updateResult(MutationTestResult::Survived);
                    $this->output->write('<fg=red;options=bold>x</>');
                    $survivedCount++;

                    //                                $this->output->writeln('Mutant for '.$mutation->file->getRealPath().':'.$mutation->originalNode->getLine().' NOT killed. ('.$mutation->mutator.')');
                    $path = str_ireplace(getcwd().'/', '', $mutation->file->getRealPath());

                    $diff = <<<HTML
                    <div class="text-green">+ {$mutation->diff()['modified'][0]}</div>
                    <div class="text-red">- {$mutation->diff()['original'][0]}</div>
                    HTML;

                    render(<<<HTML
                        <div class="mx-2 flex">
                            <span>at {$path}:{$mutation->originalNode->getLine()} </span>
                            <span class="flex-1 content-repeat-[.] text-gray mx-1"></span>
                            <span>{$mutation->mutator::name()}</span>
                        </div>
                    HTML
                    );

                    render(<<<HTML
                        <div class="mx-2 mb-1 flex">
                            {$diff}
                        </div>
                    HTML
                    );

                    continue;
                }

                $mutation->updateResult(MutationTestResult::Killed);
                $this->output->write('<fg=gray;options=bold>.</>');

                //            $this->output->writeln('Mutant for '.$mutation->file->getRealPath().':'.$mutation->originalNode->getLine().' killed.');
            }
        }

        $duration = number_format(microtime(true) - $start, 2);

        $this->output->writeln([
            '',
            '',
            '  <fg=gray>Mutations:</> <fg=default><fg=red;options=bold>'.($mutationSuite->repository->survived() !== 0 ? $mutationSuite->repository->survived().' survived</><fg=gray>,</> ' : '').'<fg=yellow;options=bold>'.($mutationSuite->repository->notCovered() !== 0 ? $mutationSuite->repository->notCovered().' not covered</><fg=gray>,</> ' : '').'<fg=green;options=bold>'.($mutationSuite->repository->timedOut() !== 0 ? $mutationSuite->repository->timedOut().' timeout</><fg=gray>,</> ' : '').'<fg=green;options=bold>'.$mutationSuite->repository->killed().' killed</>',
            '  <fg=gray>Duration:</>  <fg=default>'.$duration.'s</>',
            '',
        ]);

        //        if($survivedCount){
        //            $this->output->writeln([
        //                '',
        //                '  <fg=default;bg=yellow;options=bold> WARNING </> '.$survivedCount.' of '.count($mutations).' Mutants have survived</>',
        //                '',
        //            ]);
        //        }

        exit(0);
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

    private function getProfile(): Profile
    {
        if ($this->enabledProfile === null) {
            throw ShouldNotHappen::fromMessage('No profile enabled');
        }

        return Profiles::get($this->enabledProfile);
    }

    public function getProfileFactory(): ProfileFactory
    {
        return new ProfileFactory($this->enabledProfile ?? 'default');
    }

    private function assertInitialTestRunWasSuccessful(): void
    {
        if (Facade::result()->wasSuccessful()) {
            return;
        }

        $this->output->writeln([
            '',
            '  <fg=default;bg=red;options=bold> ERROR </> Initial test run failed, aborting mutation testing.</>',
            '',
        ]);

        exit(1);
    }

    public function getEnabledProfile(): ?string
    {
        return $this->enabledProfile;
    }

    private function calculateTimeout(): int
    {
        // TODO: calculate a reasonable timeout
        return 3;
    }
}
