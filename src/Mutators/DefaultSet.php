<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Arithmetic\ArithmeticSet;
use Pest\Mutate\Mutators\Conditionals\ConditionalsSet;

class DefaultSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            ...ArithmeticSet::mutators(),
            ...ConditionalsSet::mutators(),
        ];
    }
}
