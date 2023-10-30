<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\Div;
use PhpParser\Node\Expr\AssignOp\Mul;

class MultiplyEqualToDivideEqual implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Mul;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\Mul $node */

        return new Div($node->var, $node->expr, $node->getAttributes());
    }
}
