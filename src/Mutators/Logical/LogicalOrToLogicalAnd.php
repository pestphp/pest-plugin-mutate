<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;

class LogicalOrToLogicalAnd extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [LogicalOr::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var LogicalOr $node */
        return new LogicalAnd($node->left, $node->right);
    }
}
