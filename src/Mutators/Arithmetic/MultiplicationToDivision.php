<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\BinaryOp\Mul;

class MultiplicationToDivision implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Mul::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Mul;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Mul $node */

        return new Div($node->left, $node->right);
    }
}
