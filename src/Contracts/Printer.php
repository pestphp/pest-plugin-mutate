<?php

declare(strict_types=1);

namespace Pest\Mutate\Contracts;

use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;

interface Printer
{
    public function reportKilledMutation(MutationTest $test): void;

    public function reportSurvivedMutation(MutationTest $test): void;

    public function reportNotCoveredMutation(MutationTest $test): void;

    public function reportTimedOutMutation(MutationTest $test): void;

    public function printFilename(MutationTestCollection $testCollection): void;
}
