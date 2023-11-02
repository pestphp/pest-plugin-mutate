<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalXor;

class LogicalXorToLogicalAnd implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [LogicalXor::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof LogicalXor;
    }

    public static function mutate(Node $node): Node
    {
        /** @var LogicalXor $node */
        return new LogicalAnd($node->left, $node->right);
    }
}
