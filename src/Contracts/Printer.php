<?php

namespace Pest\Mutate\Contracts;

use Pest\Mutate\MutationTest;

interface Printer
{
    public function reportKilledMutation(MutationTest $test): void;
    public function reportSurvivedMutation(MutationTest $test): void;
    public function reportNotCoveredMutation(MutationTest $test): void;
    public function reportTimedOutMutation(MutationTest $test): void;
}
