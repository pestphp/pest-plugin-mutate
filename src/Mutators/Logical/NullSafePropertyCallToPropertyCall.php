<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;

class NullSafePropertyCallToPropertyCall implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [NullsafePropertyFetch::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof NullsafePropertyFetch;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\NullsafePropertyFetch $node */

        return new PropertyFetch($node->var, $node->name, $node->getAttributes());
    }
}
