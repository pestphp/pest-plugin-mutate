<?php

declare(strict_types=1);

namespace Pest\Mutate\Support\Printers;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\MutationSuite;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;

class DefaultPrinter implements Printer
{
    public function __construct(protected readonly OutputInterface $output)
    {
    }

    public function reportKilledMutation(MutationTest $test): void
    {
        //        $this->output->write('<fg=gray;options=bold>.</>');
    }

    public function reportSurvivedMutation(MutationTest $test): void
    {
        //        $this->output->write('<fg=red;options=bold>x</>');

        $path = str_ireplace(getcwd().'/', '', $test->mutation->file->getRealPath());

        $diff = <<<HTML
                    <div class="text-green">+ {$test->mutation->diff()['modified'][0]}</div>
                    <div class="text-red">- {$test->mutation->diff()['original'][0]}</div>
                    HTML;

        render(<<<HTML
                        <div class="mx-2 flex">
                            <span>at {$path}:{$test->mutation->originalNode->getLine()} </span>
                            <span class="flex-1 content-repeat-[.] text-gray mx-1"></span>
                            <span>{$test->mutation->mutator::name()}</span>
                        </div>
                    HTML
        );

        render(<<<HTML
                        <div class="mx-2 mb-1 flex">
                            {$diff}
                        </div>
                    HTML
        );

    }

    public function reportNotCoveredMutation(MutationTest $test): void
    {
        //        $this->output->write('<fg=yellow;options=bold>-</>');
        $this->output->writeln('No tests found for mutation: '.$test->mutation->file->getRealPath().':'.$test->mutation->originalNode->getLine().' ('.$test->mutation->mutator::name().')');
    }

    public function reportTimedOutMutation(MutationTest $test): void
    {
        //        $this->output->write('<fg=yellow;options=bold>t</>');
        $this->output->writeln('Mutant for '.$test->mutation->file->getRealPath().':'.$test->mutation->originalNode->getLine().' timed out. ('.$test->mutation->mutator.')');
    }

    public function printFilename(MutationTestCollection $testCollection): void
    {
        $this->output->writeln($testCollection->file->getRealPath());
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
            '',
        ]);
    }

    public function reportMutationSuiteFinished(MutationSuite $mutationSuite): void
    {
        $duration = number_format($mutationSuite->duration(), 2);

        $this->output->writeln([
            '',
            '',
            '  <fg=gray>Mutations:</> <fg=default><fg=red;options=bold>'.($mutationSuite->repository->survived() !== 0 ? $mutationSuite->repository->survived().' survived</><fg=gray>,</> ' : '').'<fg=yellow;options=bold>'.($mutationSuite->repository->notCovered() !== 0 ? $mutationSuite->repository->notCovered().' not covered</><fg=gray>,</> ' : '').'<fg=green;options=bold>'.($mutationSuite->repository->timedOut() !== 0 ? $mutationSuite->repository->timedOut().' timeout</><fg=gray>,</> ' : '').'<fg=green;options=bold>'.$mutationSuite->repository->killed().' killed</>',
        ]);

        $this->output->writeln('  <fg=gray>Duration:</>  <fg=default>'.$duration.'s</>');

        $this->output->writeln('');
    }
}
