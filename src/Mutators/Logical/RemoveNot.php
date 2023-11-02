<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;

class RemoveNot extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [BooleanNot::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BooleanNot $node */

        return $node->expr;
    }
}
