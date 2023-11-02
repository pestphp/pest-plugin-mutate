<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\Minus;
use PhpParser\Node\Expr\AssignOp\Plus;

class PlusEqualToMinusEqual extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Plus::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Plus $node */

        return new Minus($node->var, $node->expr, $node->getAttributes());
    }
}
