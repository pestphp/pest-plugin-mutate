<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\ShiftLeft;
use PhpParser\Node\Expr\BinaryOp\ShiftRight;

class ShiftLeftToShiftRight implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [ShiftLeft::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof ShiftLeft;
    }

    public static function mutate(Node $node): Node
    {
        /** @var ShiftLeft $node */

        return new ShiftRight($node->left, $node->right, $node->getAttributes());
    }
}
