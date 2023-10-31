<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Math;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class MinToMax extends AbstractFunctionReplaceMutator
{
    public static function from(): string
    {
        return 'min';
    }

    public static function to(): string
    {
        return 'max';
    }
}