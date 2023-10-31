<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\String\ConcatRemoveLeft;
use Pest\Mutate\Mutators\String\ConcatRemoveRight;
use Pest\Mutate\Mutators\String\ConcatSwitchSides;

class StringSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            ConcatRemoveLeft::class,
            ConcatRemoveRight::class,
            ConcatSwitchSides::class,
        ];
    }
}
