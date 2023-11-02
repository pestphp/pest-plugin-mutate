<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Expr\BinaryOp\Pow;

class PowerToMultiplication extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Pow::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Pow $node */

        return new Mul($node->left, $node->right, $node->getAttributes());
    }
}
