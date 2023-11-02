<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Minus;
use PhpParser\Node\Expr\BinaryOp\Plus;

class PlusToMinus implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Plus::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Plus;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Plus $node */

        return new Minus($node->left, $node->right);
    }
}
