<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\Mul;
use PhpParser\Node\Expr\AssignOp\Pow;

class PowerEqualToMultiplyEqual implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Pow;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\Pow $node */

        return new Mul($node->var, $node->expr, $node->getAttributes());
    }
}
