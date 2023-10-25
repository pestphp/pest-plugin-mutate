<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Mutators\Arithmetic\ArithmeticMinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\ArithmeticPlusToMinus;
use Pest\Mutate\Mutators\Arithmetic\ArithmeticSet;
use Pest\Mutate\Mutators\DefaultSet;

class Mutators
{
    final public const SET_ARITHMETIC = ArithmeticSet::class;

    final public const SET_DEFAULT = DefaultSet::class;

    final public const ARITHMETIC_PLUS_TO_MINUS = ArithmeticPlusToMinus::class;

    final public const ARITHMETIC_MINUS_TO_PLUS = ArithmeticMinusToPlus::class;
}
