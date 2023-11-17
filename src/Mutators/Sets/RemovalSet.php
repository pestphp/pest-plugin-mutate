<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Concerns\HasName;
use Pest\Mutate\Mutators\Removal\RemoveFunctionCall;
use Pest\Mutate\Mutators\Removal\RemoveMethodCall;

class RemovalSet implements MutatorSet
{
    use HasName;

    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            RemoveFunctionCall::class,
            RemoveMethodCall::class,
        ];
    }
}
