<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Arithmetic\SetArithmetic;
use Pest\Mutate\Mutators\Conditionals\IfAlwaysFalse;
use Pest\Mutate\Mutators\Conditionals\IfAlwaysTrue;
use Pest\Mutate\Mutators\DefaultSet;

class Mutators
{
    /** Sets */
    final public const SET_ARITHMETIC = SetArithmetic::class;

    final public const SET_DEFAULT = DefaultSet::class;

    /** Arithmetic */
    final public const ARITHMETIC_PLUS_TO_MINUS = PlusToMinus::class;

    final public const ARITHMETIC_MINUS_TO_PLUS = MinusToPlus::class;

    /** Conditionals */
    final public const CONDITIONALS_IF_ALWAYS_FALSE = IfAlwaysFalse::class;

    final public const CONDITIONALS_IF_ALWAYS_TRUE = IfAlwaysTrue::class;
}
