<?php

declare(strict_types=1);

namespace Pest\Mutate\Support\Printers;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\MutationSuite;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;
use Pest\Mutate\Support\MutationTestResult;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;

class DefaultPrinter implements Printer
{
    public function __construct(protected readonly OutputInterface $output)
    {
    }

    public function reportKilledMutation(MutationTest $test): void
    {
        $this->writeMutationTestLine('green', '✓', $test);
    }

    public function reportSurvivedMutation(MutationTest $test): void
    {
        $this->writeMutationTestLine('red', '⨯', $test);
    }

    public function reportNotCoveredMutation(MutationTest $test): void
    {
        $this->writeMutationTestLine('yellow', '-', $test);

        //        $this->output->writeln('No tests found for mutation: '.$test->mutation->file->getRealPath().':'.$test->mutation->originalNode->getLine().' ('.$test->mutation->mutator::name().')');
    }

    public function reportTimedOutMutation(MutationTest $test): void
    {
        $this->writeMutationTestLine('yellow', 't', $test);

        //        $this->output->writeln('Mutant for '.$test->mutation->file->getRealPath().':'.$test->mutation->originalNode->getLine().' timed out. ('.$test->mutation->mutator.')');
    }

    public function printFilename(MutationTestCollection $testCollection): void
    {
        $path = str_ireplace(getcwd().'/', '', $testCollection->file->getRealPath());

        $this->output->writeln('');
        $this->output->writeln('  <fg=default;bg=gray;options=bold> RUN </> '.$path);
    }

    public function reportError(string $message): void
    {
        $this->output->writeln([
            '',
            '  <fg=default;bg=red;options=bold> ERROR </> '.$message.'</>',
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
    }

    public function reportMutationSuiteFinished(MutationSuite $mutationSuite): void
    {
        $this->writeMutationSuiteSummary($mutationSuite);

        $this->output->writeln([
            '',
            '',
            '  <fg=gray>Mutations:</> <fg=default><fg=red;options=bold>'.($mutationSuite->repository->survived() !== 0 ? $mutationSuite->repository->survived().' survived</><fg=gray>,</> ' : '').'<fg=yellow;options=bold>'.($mutationSuite->repository->notCovered() !== 0 ? $mutationSuite->repository->notCovered().' not covered</><fg=gray>,</> ' : '').'<fg=yellow;options=bold>'.($mutationSuite->repository->notRun() !== 0 ? $mutationSuite->repository->notRun().' pending</><fg=gray>,</> ' : '').'<fg=green;options=bold>'.($mutationSuite->repository->timedOut() !== 0 ? $mutationSuite->repository->timedOut().' timeout</><fg=gray>,</> ' : '').'<fg=green;options=bold>'.$mutationSuite->repository->killed().' killed</>',
        ]);

        $duration = number_format($mutationSuite->duration(), 2);
        $this->output->writeln('  <fg=gray>Duration:</>  <fg=default>'.$duration.'s</>');

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

        $diff = '';
        foreach ($test->mutation->diff['modified'] as $line) {
            $line = htmlentities((string) $line); // TODO: this is not good, but currently required, otherwise printer breaks on `$this->foo()` because of the >
            $diff .= "<div class='text-green'>+ {$line}</div>";
        }
        foreach ($test->mutation->diff['original'] as $line) {
            $line = htmlentities($line); // TODO: this is not good, but currently required, otherwise printer breaks on `$this->foo()` because of the >
            $diff .= "<div class='text-red'>- {$line}</div>";
        }

        render(<<<HTML
                        <div class="mx-2 flex">
                            {$diff}
                        </div>
                    HTML
        );
    }
}
