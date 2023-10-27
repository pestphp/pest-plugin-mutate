<?php

declare(strict_types=1);

namespace Pest\Mutate\Tester;

use Pest\Exceptions\ShouldNotHappen;
use Pest\Mutate\Contracts\MutationTestRunner as MutationTestRunnerContract;
use Pest\Mutate\Factories\ProfileFactory;
use Pest\Mutate\Profile;
use Pest\Mutate\Profiles;
use Pest\Mutate\Support\MutationGenerator;
use Pest\Support\Container;
use Pest\Support\Coverage;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\TestRunner\TestResult\Facade;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

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
        if (getenv('MUTATION_TESTING') !== false) {
            return;
        }

        $this->enabledProfile = $profile;
    }

    public function isEnabled(): bool
    {
        if ($this->enabledProfile === Profile::FAKE) {
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

        if (! file_exists($reportPath = Coverage::getPath())) {
            // TODO: maybe we can run without a coverage report, but it is really in performant
            $this->output->writeln('No coverage report found, aborting mutation testing.');
            exit(1);
        }

        $this->output->writeln('Running mutation tests for profile: '.$this->enabledProfile);

        /** @var CodeCoverage $codeCoverage */
        $codeCoverage = require $reportPath;
        $coveredLines = array_map(fn (array $lines): array => array_filter($lines, fn (array $tests): bool => $tests !== []), $codeCoverage->getData()->lineCoverage());
        $coveredLines = array_filter($coveredLines, fn (array $lines): bool => $lines !== []);

        $files = $this->getFiles(array_keys($coveredLines));

        $mutations = [];

        /** @var MutationGenerator $generator */
        $generator = Container::getInstance()->get(MutationGenerator::class);
        foreach ($files as $file) {
            $mutations = [
                ...$mutations,
                ...$generator->generate(
                    file: $file,
                    mutators: $this->getProfile()->mutators,
                    linesToMutate: $this->getProfile()->coveredOnly ? (array_keys($coveredLines[$file->getRealPath()] ?? [])) : [],
                ),
            ];
        }

        // run tests for each mutation
        foreach ($mutations as $mutation) {
            $prettyPrinter = new Standard();
            $modifiedSource = $prettyPrinter->prettyPrintFile($mutation->modifiedAst);

            /** @var string $tmpfname */
            $tmpfname = tempnam('/tmp', 'pest_mutation_');
            file_put_contents($tmpfname, $modifiedSource);

            // TODO: we should pass the tests to run in another way, maybe via cache, mutation or env variable
            $filters = [];
            foreach ($coveredLines[$mutation->file->getRealPath()][$mutation->originalNode->getLine()] ?? [] as $test) {
                preg_match('/\\\\([a-zA-Z0-9]*)::__pest_evaluable_([^#]*)"?/', (string) $test, $matches);
                $filters[] = $matches[1].'::'.preg_replace(['/([^_])_([^_])/', '/__/'], ['$1 $2', '_'], $matches[2]);
                $filters = array_unique($filters);
            }

            if ($filters === []) {
                $this->output->writeln('No tests found for mutation: '.$mutation->file->getRealPath().':'.$mutation->originalNode->getLine());

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
                    'MUTATION_TESTING' => $mutation->file->getRealPath(),
                    'MUTATION_FILE' => $tmpfname,
                ]
            );
            $process->run();

            if ($process->isSuccessful()) {
                $this->output->write($process->getOutput());

                $this->output->writeln('Mutant for '.$mutation->file->getRealPath().':'.$mutation->originalNode->getLine().' NOT killed.');

                continue;
            }

            $this->output->writeln('Mutant for '.$mutation->file->getRealPath().':'.$mutation->originalNode->getLine().' killed.');
        }

        //        try {
        //            $this->runInitialTestRun();
        //        }catch (Exception $ex){
        //            exit(1);
        //        }

        exit(0);
    }

    //    private function runInitialTestRun()
    //    {
    //        $this->output->writeln('Start initial test run');
    //
    //        $process = new Process($this->originalArguments, env: ['MUTATION_TESTING' => 'initial']);
    //        $process->run();
    //
    //        if (!$process->isSuccessful()) {
    //            $this->output->write($process->getOutput());
    //
    //            $this->output->writeln('Initial test run failed, aborting mutation testing.');
    //            exit(1);
    //        }
    //
    //        $this->output->writeln('Finished initial test run');
    //    }

    /**
     * @param  array<array-key, string>|string  $paths
     */
    private function getFiles(array|string $paths): Finder
    {
        $dirs = [];
        $filePaths = [];
        foreach (is_string($paths) ? [$paths] : $paths as $path) {
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
        if ($this->enabledProfile === null) {
            throw ShouldNotHappen::fromMessage('No profile enabled');
        }

        return new ProfileFactory($this->enabledProfile);
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
}
