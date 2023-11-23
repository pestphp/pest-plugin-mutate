<?php

declare(strict_types=1);

namespace Pest\Mutate\Support\Printers;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\MutationSuite;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Support\MutationTestResult;
use Pest\Support\Container;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;

class DefaultPrinter implements Printer
{
    private bool $compact = false;

    public function __construct(protected readonly OutputInterface $output)
    {
    }

    public function compact(): void
    {
        $this->compact = true;
    }

    public function reportKilledMutation(MutationTest $test): void
    {
        if ($this->compact) {
            $this->output->write('<fg=gray;options=bold>.</>');

            return;
        }

        $this->writeMutationTestLine('green', '✓', $test);
    }

    public function reportSurvivedMutation(MutationTest $test): void
    {
        if ($this->compact) {
            $this->output->write('<fg=red;options=bold>x</>');

            return;
        }

        $this->writeMutationTestLine('red', '⨯', $test);
    }

    public function reportNotCoveredMutation(MutationTest $test): void
    {
        if ($this->compact) {
            $this->output->write('<fg=yellow;options=bold>-</>');

            return;
        }

        $this->writeMutationTestLine('yellow', '-', $test);

        //        $this->output->writeln('No tests found for mutation: '.$test->mutation->file->getRealPath().':'.$test->mutation->originalNode->getLine().' ('.$test->mutation->mutator::name().')');
    }

    public function reportTimedOutMutation(MutationTest $test): void
    {
        if ($this->compact) {
            $this->output->write('<fg=yellow;options=bold>t</>');

            return;
        }

        $this->writeMutationTestLine('yellow', 't', $test);
    }

    public function printFilename(MutationTestCollection $testCollection): void
    {
        if ($this->compact) {
            return;
        }

        $path = str_ireplace(getcwd().'/', '', $testCollection->file->getRealPath());

        $this->output->writeln('');
        $this->output->writeln('  <fg=default;bg=gray;options=bold> RUN </> '.$path);
    }

    public function reportError(string $message): void
    {
        $this->output->writeln([
            '',
            '  <fg=default;bg=red;options=bold> ERROR </> <fg=default>'.$message.'</>',
            '',
        ]);
    }

    public function reportScoreNotReached(float $scoreReached, float $scoreRequired): void
    {
        $this->output->writeln([
            '',
            '  <fg=white;bg=red;options=bold> FAIL </> Code coverage below expected:<fg=red;options=bold> '.number_format($scoreReached, 1).' %</>. Minimum:<fg=white;options=bold> '.number_format($scoreRequired, 1).' %</>.',
            '',
        ]);
    }

    public function reportMutationGenerationStarted(MutationSuite $mutationSuite): void
    {
        $this->output->writeln('  Generating mutations ...');
    }

    public function reportMutationGenerationFinished(MutationSuite $mutationSuite): void
    {
        $this->output->writeln([
            '  <fg=gray>'.$mutationSuite->repository->total().' Mutations for '.$mutationSuite->repository->count().' Files created</>',
            '',
        ]);
    }

    public function reportMutationSuiteStarted(MutationSuite $mutationSuite): void
    {
        $this->output->writeln([
            '  Running mutation tests:',
        ]);

        if ($this->compact) {
            $this->output->writeln('');
            $this->output->write('  ');  // ensure proper indentation before compact test output
        }
    }

    public function reportMutationSuiteFinished(MutationSuite $mutationSuite): void
    {
        if ($this->compact) {
            $this->output->writeln(''); // add new line after compact test output
        }

        $this->writeMutationSuiteSummary($mutationSuite);

        $this->output->writeln([
            '',
            '',
            '  <fg=gray>Mutations:</> <fg=default>'.($mutationSuite->repository->survived() !== 0 ? '<fg=red;options=bold>'.$mutationSuite->repository->survived().' survived</><fg=gray>,</> ' : '').($mutationSuite->repository->notCovered() !== 0 ? '<fg=yellow;options=bold>'.$mutationSuite->repository->notCovered().' not covered</><fg=gray>,</> ' : '').($mutationSuite->repository->notRun() !== 0 ? '<fg=yellow;options=bold>'.$mutationSuite->repository->notRun().' pending</><fg=gray>,</> ' : '').($mutationSuite->repository->timedOut() !== 0 ? '<fg=green;options=bold>'.$mutationSuite->repository->timedOut().' timeout</><fg=gray>,</> ' : '').'<fg=green;options=bold>'.$mutationSuite->repository->killed().' killed</>',
        ]);

        $score = number_format($mutationSuite->score(), 2);
        $this->output->writeln('  <fg=gray>Score:</>     <fg='.($mutationSuite->minScoreReached() ? 'default' : 'red').'>'.$score.'%</>');

        $duration = number_format($mutationSuite->duration(), 2);
        $this->output->writeln('  <fg=gray>Duration:</>  <fg=default>'.$duration.'s</>');

        if (Container::getInstance()->get(ConfigurationRepository::class)->mergedConfiguration()->parallel) { // @phpstan-ignore-line
            $processes = Container::getInstance()->get(ConfigurationRepository::class)->mergedConfiguration()->processes; // @phpstan-ignore-line
            $this->output->writeln('  <fg=gray>Parallel:</>  <fg=default>'.$processes.' processes</>');
        }

        $this->output->writeln('');
    }

    private function writeMutationTestLine(string $color, string $symbol, MutationTest $test): void
    {
        $this->output->writeln('  <fg='.$color.';options=bold>'.$symbol.'</> <fg=gray>Line '.$test->mutation->startLine.': '.$test->mutation->mutator::name().'</>'); // @pest-mutate-ignore
    }

    private function writeMutationSuiteSummary(MutationSuite $mutationSuite): void
    {
        foreach ($mutationSuite->repository->all() as $testCollection) {
            foreach ($testCollection->tests() as $test) {
                $this->writeMutationTestSummary($test);
            }
        }
    }

    private function writeMutationTestSummary(MutationTest $test): void
    {
        if (! in_array($test->result(), [MutationTestResult::Survived, MutationTestResult::NotCovered], true)) {
            return;
        }

        $path = str_ireplace(getcwd().'/', '', $test->mutation->file->getRealPath());

        render(<<<'HTML'
                        <div class="mx-2 mt-1 flex">
                            <span class="flex-1 content-repeat-[-] text-red"></span>
                        </div>
                    HTML
        );

        if ($test->result() === MutationTestResult::Survived) {
            $color = 'red';
            $label = 'SURVIVED';
            $error = 'Mutant has survived.';
        } else {
            $color = 'yellow';
            $label = 'NOT COVERED';
            $error = 'Mutation is not covered by any test.';
        }

        $this->output->writeln([
            '  <fg=default;bg='.$color.';options=bold> '.$label.' </> <fg=default;options=bold>'.$path.' <fg=gray> > Line '.$test->mutation->startLine.': '.$test->mutation->mutator::name().'</>', // @pest-mutate-ignore
            '  <fg=default;options=bold>'.$error.'</>',
        ]);

        $diff = $test->mutation->diff;
        $this->output->writeln($diff);

        //        render(<<<HTML
        //                        <div class="mx-2 flex">
        //                            {$diff}
        //                        </div>
        //                    HTML
        //        );
    }
}
