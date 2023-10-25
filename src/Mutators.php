<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Arithmetic\SetArithmetic;
use Pest\Mutate\Mutators\DefaultSet;

class Mutators
{
    final public const SET_ARITHMETIC = SetArithmetic::class;

    final public const SET_DEFAULT = DefaultSet::class;

    final public const ARITHMETIC_PLUS_TO_MINUS = PlusToMinus::class;

    final public const ARITHMETIC_MINUS_TO_PLUS = MinusToPlus::class;
}
