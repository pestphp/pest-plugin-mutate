<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class ArrayShiftToArrayPop extends AbstractFunctionReplaceMutator
{
    public static function from(): string
    {
        return 'array_shift';
    }

    public static function to(): string
    {
        return 'array_pop';
    }
}
