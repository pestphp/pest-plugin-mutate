<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Conditionals\IfAlwaysFalse;
use Pest\Mutate\Mutators\Conditionals\IfAlwaysTrue;
use Pest\Mutate\Mutators\Conditionals\TernaryAlwaysFalse;
use Pest\Mutate\Mutators\Conditionals\TernaryAlwaysTrue;
use Pest\Mutate\Mutators\Conditionals\WhileAlwaysFalse;

class ConditionalsSet implements MutatorSet
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
