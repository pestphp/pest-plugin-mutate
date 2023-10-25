<?php

declare(strict_types=1);

namespace Pest\Mutate\Contracts;

interface MutationTester
{
    public function run(): void;
}
