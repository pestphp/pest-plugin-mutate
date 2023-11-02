<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

class BooleanAndToBooleanOr implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [BooleanAnd::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof BooleanAnd;
    }

    public static function mutate(Node $node): Node
    {
        /** @var BooleanAnd $node */
        return new BooleanOr($node->left, $node->right);
    }
}
