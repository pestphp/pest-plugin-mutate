<?php

declare(strict_types=1);

namespace Pest\Mutate\Support\Printers;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\MutationSuite;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;

class CompactPrinter extends DefaultPrinter implements Printer
{
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

    public function printFilename(MutationTestCollection $testCollection): void
    {
        // nothing to do here
    }

    public function reportMutationSuiteStarted(MutationSuite $mutationSuite): void
    {
        parent::reportMutationSuiteStarted($mutationSuite);

        $this->output->writeln('');
        $this->output->write('  ');  // ensure proper indentation before compact test output
    }

    public function reportMutationSuiteFinished(MutationSuite $mutationSuite): void
    {
        $this->output->writeln(''); // add new line after compact test output

        parent::reportMutationSuiteFinished($mutationSuite);
    }
}
