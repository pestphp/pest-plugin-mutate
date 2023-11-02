<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Abstract;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;

abstract class AbstractMutator implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return in_array($node::class, static::nodesToHandle(), true);
    }
}
