<?php

namespace Pest\Mutate\Support\Printers;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\MutationTest;
use Symfony\Component\Console\Output\ConsoleOutput;

class CompactPrinter implements Printer
{
    public function __construct(private ConsoleOutput $output)
    {
    }

    public function reportKilledMutation(MutationTest $test): void
    {
        $this->output->write('<fg=gray;options=bold>.</>');
    }

    public function reportSurvivedMutation(MutationTest $test): void
    {
        $this->output->write('<fg=red;options=bold>x</>');
    }

    public function reportNotCoveredMutation(MutationTest $test): void
    {
        $this->output->write('<fg=yellow;options=bold>-</>');
    }

    public function reportTimedOutMutation(MutationTest $test): void
    {
        $this->output->write('<fg=yellow;options=bold>t</>');
    }
}
