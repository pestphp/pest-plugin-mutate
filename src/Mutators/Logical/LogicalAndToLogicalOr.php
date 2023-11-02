<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;

class LogicalAndToLogicalOr implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [LogicalAnd::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof LogicalAnd;
    }

    public static function mutate(Node $node): Node
    {
        /** @var LogicalAnd $node */
        return new LogicalOr($node->left, $node->right);
    }
}
