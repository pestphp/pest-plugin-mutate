<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BitwiseAnd;
use PhpParser\Node\Expr\BinaryOp\BitwiseOr;

class BitwiseOrToBitwiseAnd implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [BitwiseOr::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof BitwiseOr;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\BitwiseOr $node */

        return new BitwiseAnd($node->left, $node->right);
    }
}
