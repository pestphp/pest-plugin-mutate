<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\Div;
use PhpParser\Node\Expr\AssignOp\Mul;

class MultiplyEqualToDivideEqual extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Mul::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\Mul $node */

        return new Div($node->var, $node->expr, $node->getAttributes());
    }
}
