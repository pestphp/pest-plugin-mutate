<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\ShiftLeft;
use PhpParser\Node\Expr\AssignOp\ShiftRight;

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
        /** @var Node\Expr\AssignOp\ShiftLeft $node */

        return new ShiftRight($node->var, $node->expr, $node->getAttributes());
    }
}
