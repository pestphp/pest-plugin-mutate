<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapArrayDiffKey extends AbstractFunctionCallUnwrapMutator
{
    public static function functionName(): string
    {
        return 'array_diff_key';
    }
}
