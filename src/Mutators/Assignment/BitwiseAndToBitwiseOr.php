<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\BitwiseAnd;
use PhpParser\Node\Expr\AssignOp\BitwiseOr;

class BitwiseAndToBitwiseOr extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [BitwiseAnd::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\BitwiseAnd $node */

        return new BitwiseOr($node->var, $node->expr, $node->getAttributes());
    }
}
