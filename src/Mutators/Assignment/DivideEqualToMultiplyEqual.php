<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\Div;
use PhpParser\Node\Expr\AssignOp\Mul;

class DivideEqualToMultiplyEqual extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Div::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\Div $node */

        return new Mul($node->var, $node->expr, $node->getAttributes());
    }
}
