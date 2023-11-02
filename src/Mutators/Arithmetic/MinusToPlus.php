<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Minus;
use PhpParser\Node\Expr\BinaryOp\Plus;

class MinusToPlus implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Minus::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Minus;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Minus $node */

        return new Plus($node->left, $node->right);
    }
}
