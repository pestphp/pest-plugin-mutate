<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Conditionals;

use Pest\Mutate\Contracts\MutatorSet;

class SetConditionals implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            IfAlwaysFalse::class,
            IfAlwaysTrue::class,
            TernaryAlwaysFalse::class,
            TernaryAlwaysTrue::class,
            WhileAlwaysFalse::class,
        ];
    }
}
