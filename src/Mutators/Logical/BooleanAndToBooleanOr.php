<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

class BooleanAndToBooleanOr extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [BooleanAnd::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var BooleanAnd $node */
        return new BooleanOr($node->left, $node->right);
    }
}
