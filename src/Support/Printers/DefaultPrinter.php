<?php

declare(strict_types=1);

namespace Pest\Mutate\Support\Printers;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;

class DefaultPrinter implements Printer
{
    public function __construct(private readonly OutputInterface $output)
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
}
