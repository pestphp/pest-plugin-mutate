<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Expr\BinaryOp\Pow;

class PowerToMultiplication implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Pow;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Pow $node */

        return new Mul($node->left, $node->right, $node->getAttributes());
    }
}
