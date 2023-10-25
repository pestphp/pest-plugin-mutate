<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Conditionals\IfAlwaysFalse;
use Pest\Mutate\Mutators\Conditionals\IfAlwaysTrue;
use Pest\Mutate\Mutators\Conditionals\TernaryAlwaysFalse;
use Pest\Mutate\Mutators\Conditionals\TernaryAlwaysTrue;
use Pest\Mutate\Mutators\Conditionals\WhileAlwaysFalse;
use Pest\Mutate\Mutators\Sets\ArithmeticSet;
use Pest\Mutate\Mutators\Sets\ConditionalsSet;
use Pest\Mutate\Mutators\Sets\DefaultSet;

class Mutators
{
    /** Sets */
    final public const SET_DEFAULT = DefaultSet::class;

    final public const SET_ARITHMETIC = ArithmeticSet::class;

    final public const SET_CONDITIONALS = ConditionalsSet::class;

    /** Arithmetic */
    final public const ARITHMETIC_PLUS_TO_MINUS = PlusToMinus::class;

    final public const ARITHMETIC_MINUS_TO_PLUS = MinusToPlus::class;

    /** Conditionals */
    final public const CONDITIONALS_IF_ALWAYS_FALSE = IfAlwaysFalse::class;

    final public const CONDITIONALS_IF_ALWAYS_TRUE = IfAlwaysTrue::class;

    final public const CONDITIONALS_TERNARY_ALWAYS_FALSE = TernaryAlwaysFalse::class;

    final public const CONDITIONALS_TERNARY_ALWAYS_TRUE = TernaryAlwaysTrue::class;

    final public const CONDITIONALS_WHILE_ALWAYS_TRUE = WhileAlwaysFalse::class;
}
