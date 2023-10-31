<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Math;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class MaxToMin extends AbstractFunctionReplaceMutator
{
    public static function from(): string
    {
        return 'max';
    }

    public static function to(): string
    {
        return 'min';
    }
}
