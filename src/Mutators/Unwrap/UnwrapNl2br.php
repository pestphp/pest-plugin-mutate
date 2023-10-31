<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Unwrap;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapNl2br extends AbstractFunctionCallUnwrapMutator
{
    public static function functionName(): string
    {
        return 'nl2br';
    }
}
