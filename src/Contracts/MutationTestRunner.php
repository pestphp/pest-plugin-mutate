<?php

declare(strict_types=1);

namespace Pest\Mutate\Contracts;

use Pest\Mutate\Factories\ProfileFactory;

interface MutationTestRunner
{
    public function enable(string $profile): void;

    public function isEnabled(): bool;

    /**
     * @param  array<int, string>  $arguments
     */
    public function setOriginalArguments(array $arguments): void;

    public function isCodeCoverageRequested(): bool;

    public function getProfileFactory(): ProfileFactory;

    public function getEnabledProfile(): ?string;

    public function run(): void;
}
