<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class ArrayPopToArrayShift extends AbstractFunctionReplaceMutator
{
    public static function from(): string
    {
        return 'array_pop';
    }

    public static function to(): string
    {
        return 'array_shift';
    }
}
