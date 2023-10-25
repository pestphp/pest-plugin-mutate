<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Conditionals;

use Pest\Mutate\Contracts\MutatorSet;

class ConditionalsSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            ConditionalsIfAlwaysFalse::class,
            ConditionalsIfAlwaysTrue::class,
            ConditionalsTernaryAlwaysFalse::class,
            ConditionalsTernaryAlwaysTrue::class,
            ConditionalsWhileAlwaysFalse::class,
        ];
    }
}
