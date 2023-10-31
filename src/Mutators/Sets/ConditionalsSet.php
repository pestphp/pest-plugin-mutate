<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Conditionals\DoWhileAlwaysFalse;
use Pest\Mutate\Mutators\Conditionals\ElseIfNegated;
use Pest\Mutate\Mutators\Conditionals\IfNegated;
use Pest\Mutate\Mutators\Conditionals\TernaryNegated;
use Pest\Mutate\Mutators\Conditionals\WhileAlwaysFalse;

class ConditionalsSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            IfNegated::class,
            ElseIfNegated::class,
            TernaryNegated::class,
            DoWhileAlwaysFalse::class,
            WhileAlwaysFalse::class,
        ];
    }
}
