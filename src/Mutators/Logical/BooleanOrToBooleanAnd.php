<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

class BooleanOrToBooleanAnd extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [BooleanOr::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var BooleanOr $node */
        return new BooleanAnd($node->left, $node->right);
    }
}
