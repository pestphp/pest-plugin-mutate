<?php

declare(strict_types=1);

namespace Pest\Mutate\Contracts;

use Pest\Mutate\MutationSuite;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;

interface Printer
{
    public function compact(): void;

    public function reportCaughtMutation(MutationTest $test): void;

    public function reportEscapedMutation(MutationTest $test): void;

    public function reportNotCoveredMutation(MutationTest $test): void;

    public function reportTimedOutMutation(MutationTest $test): void;

    public function reportError(string $message): void;

    public function reportScoreNotReached(float $scoreReached, float $scoreRequired): void;

    public function printFilename(MutationTestCollection $testCollection): void;

    public function reportMutationGenerationStarted(MutationSuite $mutationSuite): void;

    public function reportMutationGenerationFinished(MutationSuite $mutationSuite): void;

    public function reportMutationSuiteStarted(MutationSuite $mutationSuite): void;

    public function reportMutationSuiteFinished(MutationSuite $mutationSuite): void;
}
