<?php

declare(strict_types=1);

namespace Pest\Mutate\Contracts;

interface MutationTestRunner
{
    public function enable(string $profile): void;

    public function isEnabled(): bool;

    public function isCodeCoverageRequested(): bool;

    public function run(): void;
}
