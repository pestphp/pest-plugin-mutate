<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;

class NullSafeMethodCallToMethodCall extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [NullsafeMethodCall::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\NullsafeMethodCall $node */

        return new MethodCall($node->var, $node->name, $node->getRawArgs(), $node->getAttributes());
    }
}
