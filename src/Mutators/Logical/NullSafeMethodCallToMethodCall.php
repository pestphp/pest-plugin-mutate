<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;

class NullSafeMethodCallToMethodCall implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof NullsafeMethodCall;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\NullsafeMethodCall $node */

        return new MethodCall($node->var, $node->name, $node->getRawArgs(), $node->getAttributes());
    }
}
