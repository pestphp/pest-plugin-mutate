<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

class BooleanOrToBooleanAnd implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof BooleanOr;
    }

    public static function mutate(Node $node): Node
    {
        /** @var BooleanOr $node */
        return new BooleanAnd($node->left, $node->right);
    }
}
