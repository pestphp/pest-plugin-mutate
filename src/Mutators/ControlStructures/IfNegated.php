<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Stmt\If_;

class IfNegated extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [If_::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var If_ $node */
        $node->cond = new BooleanNot($node->cond);

        return $node;
    }
}
