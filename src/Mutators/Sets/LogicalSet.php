<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Logical\BooleanAndToBooleanOr;
use Pest\Mutate\Mutators\Logical\BooleanOrToBooleanAnd;
use Pest\Mutate\Mutators\Logical\CoalesceSwitchSides;
use Pest\Mutate\Mutators\Logical\ConcatSwitchSides;
use Pest\Mutate\Mutators\Logical\FalseToTrue;
use Pest\Mutate\Mutators\Logical\InstanceOfToFalse;
use Pest\Mutate\Mutators\Logical\InstanceOfToTrue;
use Pest\Mutate\Mutators\Logical\LogicalAndToLogicalOr;
use Pest\Mutate\Mutators\Logical\LogicalOrToLogicalAnd;
use Pest\Mutate\Mutators\Logical\LogicalXorToLogicalAnd;
use Pest\Mutate\Mutators\Logical\TrueToFalse;

class LogicalSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            BooleanAndToBooleanOr::class,
            BooleanOrToBooleanAnd::class,
            CoalesceSwitchSides::class,
            ConcatSwitchSides::class,
            LogicalAndToLogicalOr::class,
            LogicalOrToLogicalAnd::class,
            LogicalXorToLogicalAnd::class,
            FalseToTrue::class,
            TrueToFalse::class,
            InstanceOfToTrue::class,
            InstanceOfToFalse::class,
        ];
    }
}
