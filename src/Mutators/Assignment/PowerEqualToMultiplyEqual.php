<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\Mul;
use PhpParser\Node\Expr\AssignOp\Pow;

class PowerEqualToMultiplyEqual extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Pow::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\Pow $node */

        return new Mul($node->var, $node->expr, $node->getAttributes());
    }
}
