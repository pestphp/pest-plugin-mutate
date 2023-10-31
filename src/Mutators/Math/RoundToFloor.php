<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Math;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class RoundToFloor extends AbstractFunctionReplaceMutator
{
    public static function from(): string
    {
        return 'round';
    }

    public static function to(): string
    {
        return 'floor';
    }
}
