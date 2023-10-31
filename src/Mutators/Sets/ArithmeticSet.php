<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Arithmetic\BitwiseAndToBitwiseOr;
use Pest\Mutate\Mutators\Arithmetic\BitwiseOrToBitwiseAnd;
use Pest\Mutate\Mutators\Arithmetic\BitwiseXorToBitwiseAnd;
use Pest\Mutate\Mutators\Arithmetic\DivisionToMultiplication;
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\ModulusToMultiplication;
use Pest\Mutate\Mutators\Arithmetic\MultiplicationToDivision;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Arithmetic\PowerToMultiplication;
use Pest\Mutate\Mutators\Arithmetic\ShiftLeftToShiftRight;
use Pest\Mutate\Mutators\Arithmetic\ShiftRightToShiftLeft;

class ArithmeticSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            BitwiseAndToBitwiseOr::class,
            BitwiseOrToBitwiseAnd::class,
            BitwiseXorToBitwiseAnd::class,
            PlusToMinus::class,
            MinusToPlus::class,
            DivisionToMultiplication::class,
            MultiplicationToDivision::class,
            ModulusToMultiplication::class,
            PowerToMultiplication::class,
            ShiftLeftToShiftRight::class,
            ShiftRightToShiftLeft::class,
        ];
    }
}
