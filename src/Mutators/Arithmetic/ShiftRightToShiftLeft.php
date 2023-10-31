<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\ShiftLeft;
use PhpParser\Node\Expr\BinaryOp\ShiftRight;

class ShiftRightToShiftLeft implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof ShiftRight;
    }

    public static function mutate(Node $node): Node
    {
        /** @var ShiftRight $node */

        return new ShiftLeft($node->left, $node->right, $node->getAttributes());
    }
}
