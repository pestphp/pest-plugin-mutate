<?php

declare(strict_types=1);

namespace Pest\Mutate\Tester;

use Exception;
use Pest\Mutate\Contracts\MutationTestRunner as MutationTestRunnerContract;
use Pest\Support\Container;
use Pest\Support\Coverage;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class MutationTestRunner implements MutationTestRunnerContract
{
    private ?string $enabledProfile = null;

    private bool $codeCoverageRequested = false;

    public static function fake(): void
    {
        Container::getInstance()->add(MutationTestRunnerContract::class, new MutationTestRunnerFake());
    }

    public function __construct(private readonly OutputInterface $output)
    {
    }

    public function enable(string $profile): void
    {
        //        if(getenv('MUTATION_TESTING') === 'initial'){
        //            return;
        //        }

        $this->enabledProfile = $profile;
    }

    public function isEnabled(): bool
    {
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

    /**
     * @param  array<int, string>  $arguments
     */
    public function originalArguments(array $arguments): void
    {
    }

    public function run(): void
    {
        if (! file_exists($reportPath = Coverage::getPath())) {
            // TODO: maybe we can run without a coverage report, but it is really in performant
            $this->output->writeln('No coverage report found, aborting mutation testing.');
            exit(1);
        }
        /** @var CodeCoverage $codeCoverage */
        $codeCoverage = require $reportPath;
        dump($codeCoverage->getTests());
        $this->output->writeln('Running mutation tests for profile: '.$this->enabledProfile);

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
}
