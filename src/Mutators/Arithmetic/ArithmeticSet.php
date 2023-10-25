<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\MutatorSet;

class ArithmeticSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            ArithmeticPlusToMinus::class,
            ArithmeticMinusToPlus::class,
        ];
    }
}
