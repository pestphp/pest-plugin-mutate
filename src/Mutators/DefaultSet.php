<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Arithmetic\SetArithmetic;
use Pest\Mutate\Mutators\Conditionals\SetConditionals;

class DefaultSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            ...SetArithmetic::mutators(),
            ...SetConditionals::mutators(),
        ];
    }
}
