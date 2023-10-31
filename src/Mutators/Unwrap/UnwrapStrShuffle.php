<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Unwrap;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapStrShuffle extends AbstractFunctionCallUnwrapMutator
{
    public static function functionName(): string
    {
        return 'str_shuffle';
    }
}
