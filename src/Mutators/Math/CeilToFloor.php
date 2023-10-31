<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Math;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class CeilToFloor extends AbstractFunctionReplaceMutator
{
    public static function from(): string
    {
        return 'ceil';
    }

    public static function to(): string
    {
        return 'floor';
    }
}
