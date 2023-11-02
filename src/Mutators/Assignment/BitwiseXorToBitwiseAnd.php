<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\BitwiseAnd;
use PhpParser\Node\Expr\AssignOp\BitwiseXor;

class BitwiseXorToBitwiseAnd extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [BitwiseXor::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\BitwiseXor $node */

        return new BitwiseAnd($node->var, $node->expr, $node->getAttributes());
    }
}
