<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\BinaryOp\Mul;

class DivisionToMultiplication implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Div::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Div;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Div $node */

        return new Mul($node->left, $node->right);
    }
}
