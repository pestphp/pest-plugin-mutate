<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Mutators\Arithmetic\DivisionToMultiplication;
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\ModulusToMultiplication;
use Pest\Mutate\Mutators\Arithmetic\MultiplicationToDivision;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Arithmetic\PowerToMultiplication;
use Pest\Mutate\Mutators\Assignment\BitwiseAndToBitwiseOr;
use Pest\Mutate\Mutators\Assignment\BitwiseOrToBitwiseAnd;
use Pest\Mutate\Mutators\Assignment\BitwiseXorToBitwiseAnd;
use Pest\Mutate\Mutators\Assignment\CoalesceEqualToEqual;
use Pest\Mutate\Mutators\Assignment\ConcatEqualToEqual;
use Pest\Mutate\Mutators\Assignment\DivideEqualToMultiplyEqual;
use Pest\Mutate\Mutators\Assignment\MinusEqualToPlusEqual;
use Pest\Mutate\Mutators\Assignment\ModulusEqualToMultiplyEqual;
use Pest\Mutate\Mutators\Assignment\MultiplyEqualToDivideEqual;
use Pest\Mutate\Mutators\Assignment\PlusEqualToMinusEqual;
use Pest\Mutate\Mutators\Assignment\PowerEqualToMultiplyEqual;
use Pest\Mutate\Mutators\Assignment\ShiftLeftToShiftRight;
use Pest\Mutate\Mutators\Assignment\ShiftRightToShiftLeft;
use Pest\Mutate\Mutators\Conditionals\IfAlwaysFalse;
use Pest\Mutate\Mutators\Conditionals\IfAlwaysTrue;
use Pest\Mutate\Mutators\Conditionals\TernaryAlwaysFalse;
use Pest\Mutate\Mutators\Conditionals\TernaryAlwaysTrue;
use Pest\Mutate\Mutators\Conditionals\WhileAlwaysFalse;
use Pest\Mutate\Mutators\Equality\EqualToIdentical;
use Pest\Mutate\Mutators\Equality\GreaterOrEqualToGreater;
use Pest\Mutate\Mutators\Equality\GreaterToGreaterOrEqual;
use Pest\Mutate\Mutators\Equality\IdenticalToEqual;
use Pest\Mutate\Mutators\Equality\NotEqualToNotIdentical;
use Pest\Mutate\Mutators\Equality\NotIdenticalToNotEqual;
use Pest\Mutate\Mutators\Equality\SmallerOrEqualToSmaller;
use Pest\Mutate\Mutators\Equality\SmallerToSmallerOrEqual;
use Pest\Mutate\Mutators\Equality\SpaceshipSwitchSides;
use Pest\Mutate\Mutators\Logical\BooleanAndToBooleanOr;
use Pest\Mutate\Mutators\Logical\BooleanOrToBooleanAnd;
use Pest\Mutate\Mutators\Logical\CoalesceSwitchSides;
use Pest\Mutate\Mutators\Logical\ConcatSwitchSides;
use Pest\Mutate\Mutators\Logical\LogicalAndToLogicalOr;
use Pest\Mutate\Mutators\Logical\LogicalOrToLogicalAnd;
use Pest\Mutate\Mutators\Logical\LogicalXorToLogicalAnd;
use Pest\Mutate\Mutators\Sets\ArithmeticSet;
use Pest\Mutate\Mutators\Sets\AssignmentSet;
use Pest\Mutate\Mutators\Sets\ConditionalsSet;
use Pest\Mutate\Mutators\Sets\DefaultSet;
use Pest\Mutate\Mutators\Sets\EqualitySet;
use Pest\Mutate\Mutators\Sets\LogicalSet;

class Mutators
{
    /** Sets */
    final public const SET_DEFAULT = DefaultSet::class;

    final public const SET_ARITHMETIC = ArithmeticSet::class;

    final public const SET_ASSIGNMENT = AssignmentSet::class;

    final public const SET_CONDITIONALS = ConditionalsSet::class;

    final public const SET_EQUALITY = EqualitySet::class;

    final public const SET_LOGICAL = LogicalSet::class;

    /** Arithmetic */
    final public const ARITHMETIC_BITWISE_AND_TO_BITWISE_OR = \Pest\Mutate\Mutators\Arithmetic\BitwiseAndToBitwiseOr::class;

    final public const ARITHMETIC_BITWISE_OR_TO_BITWISE_AND = \Pest\Mutate\Mutators\Arithmetic\BitwiseOrToBitwiseAnd::class;

    final public const ARITHMETIC_BITWISE_XOR_TO_BITWISE_AND = \Pest\Mutate\Mutators\Arithmetic\BitwiseXorToBitwiseAnd::class;

    final public const ARITHMETIC_PLUS_TO_MINUS = PlusToMinus::class;

    final public const ARITHMETIC_MINUS_TO_PLUS = MinusToPlus::class;

    final public const ARITHMETIC_DIVISION_TO_MULTIPLICATION = DivisionToMultiplication::class;

    final public const ARITHMETIC_MULTIPLICATION_TO_DIVISION = MultiplicationToDivision::class;

    final public const ARITHMETIC_MODULUS_TO_MULTIPLICATION = ModulusToMultiplication::class;

    final public const ARITHMETIC_POWER_TO_MULTIPLICATION = PowerToMultiplication::class;

    final public const ARITHMETIC_SHIFT_LEFT_TO_SHIFT_RIGHT = \Pest\Mutate\Mutators\Arithmetic\ShiftLeftToShiftRight::class;

    final public const ARITHMETIC_SHIFT_RIGHT_TO_SHIFT_LEFT = \Pest\Mutate\Mutators\Arithmetic\ShiftRightToShiftLeft::class;

    /** Assignments */
    final public const ASSIGNMENTS_BITWISE_AND_TO_BITWISE_OR = BitwiseAndToBitwiseOr::class;

    final public const ASSIGNMENTS_BITWISE_OR_TO_BITWISE_AND = BitwiseOrToBitwiseAnd::class;

    final public const ASSIGNMENTS_BITWISE_XOR_TO_BITWISE_AND = BitwiseXorToBitwiseAnd::class;

    final public const ASSIGNMENTS_COALESCE_EQUAL_TO_EQUAL = CoalesceEqualToEqual::class;

    final public const ASSIGNMENTS_CONCAT_EQUAL_TO_EQUAL = ConcatEqualToEqual::class;

    final public const ASSIGNMENTS_DIVIDE_EQUAL_TO_MULTIPLY_EQUAL = DivideEqualToMultiplyEqual::class;

    final public const ASSIGNMENTS_MINUS_EQUAL_TO_PLUS_EQUAL = MinusEqualToPlusEqual::class;

    final public const ASSIGNMENTS_MODULUS_EQUAL_TO_MULTIPLY_EQUAL = ModulusEqualToMultiplyEqual::class;

    final public const ASSIGNMENTS_MULTIPLY_EQUAL_TO_DIVIDE_EQUAL = MultiplyEqualToDivideEqual::class;

    final public const ASSIGNMENTS_PLUS_EQUAL_TO_MINUS_EQUAL = PlusEqualToMinusEqual::class;

    final public const ASSIGNMENTS_POWER_EQUAL_TO_MULTIPLY_EQUAL = PowerEqualToMultiplyEqual::class;

    final public const ASSIGNMENTS_SHIFT_LEFT_TO_SHIFT_RIGHT = ShiftLeftToShiftRight::class;

    final public const ASSIGNMENTS_SHIFT_RIGHT_TO_SHIFT_LEFT = ShiftRightToShiftLeft::class;

    /** Conditionals */
    final public const CONDITIONALS_IF_ALWAYS_FALSE = IfAlwaysFalse::class;

    final public const CONDITIONALS_IF_ALWAYS_TRUE = IfAlwaysTrue::class;

    final public const CONDITIONALS_TERNARY_ALWAYS_FALSE = TernaryAlwaysFalse::class;

    final public const CONDITIONALS_TERNARY_ALWAYS_TRUE = TernaryAlwaysTrue::class;

    final public const CONDITIONALS_WHILE_ALWAYS_TRUE = WhileAlwaysFalse::class;

    /** Equality */
    final public const EQUALITY_GREATER_TO_GREATER_OR_EQUAL = GreaterToGreaterOrEqual::class;

    final public const EQUALITY_GREATER_OR_EQUAL_TO_GREATER = GreaterOrEqualToGreater::class;

    final public const EQUALITY_SMALLER_TO_SMALLER_OR_EQUAL = SmallerToSmallerOrEqual::class;

    final public const EQUALITY_SMALLER_OR_EQUAL_TO_SMALLER = SmallerOrEqualToSmaller::class;

    final public const EQUALITY_EQUAL_TO_IDENTICAL = EqualToIdentical::class;

    final public const EQUALITY_IDENTICAL_TO_EQUAL = IdenticalToEqual::class;

    final public const EQUALITY_NOT_EQUAL_TO_NOT_IDENTICAL = NotEqualToNotIdentical::class;

    final public const EQUALITY_NOT_IDENTICAL_TO_NOT_EQUAL = NotIdenticalToNotEqual::class;

    final public const EQUALITY_SPACESHIP_SWITCH_SIDES = SpaceshipSwitchSides::class;

    /** Logical */
    final public const LOGICAL_BOOLEAN_AND_TO_BOOLEAN_OR = BooleanAndToBooleanOr::class;

    final public const LOGICAL_BOOLEAN_OR_TO_BOOLEAN_AND = BooleanOrToBooleanAnd::class;

    final public const LOGICAL_COALESCE_SWITCH_SIDES = CoalesceSwitchSides::class;

    final public const LOGICAL_CONCAT_SWITCH_SIDES = ConcatSwitchSides::class;

    final public const LOGICAL_LOGICAL_AND_TO_LOGICAL_OR = LogicalAndToLogicalOr::class;

    final public const LOGICAL_LOGICAL_OR_TO_LOGICAL_AND = LogicalOrToLogicalAnd::class;

    final public const LOGICAL_LOGICAL_XOR_TO_LOGICAL_AND = LogicalXorToLogicalAnd::class;
}
