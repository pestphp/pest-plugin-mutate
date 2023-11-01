<?php

declare(strict_types=1);

namespace Pest\Mutate\Tester;

use Pest\Exceptions\ShouldNotHappen;
use Pest\Mutate\Contracts\MutationTestRunner as MutationTestRunnerContract;
use Pest\Mutate\Factories\ProfileFactory;

class MutationTestRunnerFake implements MutationTestRunnerContract
{
    private ?string $enabledProfile = null;

    public function run(): void
    {
        // TODO: Implement run() method.
    }

    public function enable(string $profile): void
    {
        $this->enabledProfile = $profile;
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function isCodeCoverageRequested(): bool
    {
        return false;
    }

    public function setOriginalArguments(array $arguments): void
    {
        // TODO: Implement setOriginalArguments() method.
    }

    public function getProfileFactory(): ProfileFactory
    {
        if ($this->enabledProfile === null) {
            throw ShouldNotHappen::fromMessage('No profile enabled');
        }

        return new ProfileFactory($this->enabledProfile);
    }
}
