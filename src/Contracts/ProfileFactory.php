<?php

declare(strict_types=1);

namespace Pest\Mutate\Contracts;

interface ProfileFactory
{
    /**
     * @param  array<int, string>|string  ...$paths
     */
    public function paths(array|string ...$paths): self;

    /**
     * @param  array<int, class-string<Mutator|MutatorSet>>|class-string<Mutator|MutatorSet>  ...$mutators
     */
    public function mutators(array|string ...$mutators): self;

    public function min(float $minMSI): self;

    public function coveredOnly(bool $coveredOnly = true): self;

    public function parallel(bool $parallel = true): self;
}
