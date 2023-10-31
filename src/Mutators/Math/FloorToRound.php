<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Math;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class FloorToRound extends AbstractFunctionReplaceMutator
{
    public static function from(): string
    {
        return 'floor';
    }

    public static function to(): string
    {
        return 'round';
    }
}
