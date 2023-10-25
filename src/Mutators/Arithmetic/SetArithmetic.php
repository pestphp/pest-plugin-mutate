<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\MutatorSet;

class SetArithmetic implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            PlusToMinus::class,
            MinusToPlus::class,
        ];
    }
}
