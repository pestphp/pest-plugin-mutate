<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BitwiseAnd;
use PhpParser\Node\Expr\BinaryOp\BitwiseOr;

class BitwiseAndToBitwiseOr extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [BitwiseAnd::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\BitwiseAnd $node */

        return new BitwiseOr($node->left, $node->right);
    }
}
