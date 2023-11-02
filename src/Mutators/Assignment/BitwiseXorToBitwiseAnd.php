<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\BitwiseAnd;
use PhpParser\Node\Expr\AssignOp\BitwiseXor;

class BitwiseXorToBitwiseAnd implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [BitwiseXor::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof BitwiseXor;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\BitwiseXor $node */

        return new BitwiseAnd($node->var, $node->expr, $node->getAttributes());
    }
}
