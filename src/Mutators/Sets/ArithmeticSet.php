<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;

class ArithmeticSet implements MutatorSet
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
