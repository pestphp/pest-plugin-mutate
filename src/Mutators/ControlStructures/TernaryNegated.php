<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Ternary;

class TernaryNegated extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Ternary::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Ternary $node */
        $node->cond = new BooleanNot($node->cond);

        return $node;
    }
}
