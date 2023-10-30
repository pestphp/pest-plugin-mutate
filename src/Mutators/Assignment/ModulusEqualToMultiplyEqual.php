<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\Mod;
use PhpParser\Node\Expr\AssignOp\Mul;

class ModulusEqualToMultiplyEqual implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Mod;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\Mod $node */

        return new Mul($node->var, $node->expr, $node->getAttributes());
    }
}
