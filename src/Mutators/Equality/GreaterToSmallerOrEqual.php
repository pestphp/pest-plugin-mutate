<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;

class GreaterToSmallerOrEqual extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Greater::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Greater $node */
        return new SmallerOrEqual($node->left, $node->right);
    }
}
