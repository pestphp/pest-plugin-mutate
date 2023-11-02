<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\BitwiseAnd;
use PhpParser\Node\Expr\AssignOp\BitwiseOr;

class BitwiseAndToBitwiseOr implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [BitwiseAnd::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof BitwiseAnd;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\BitwiseAnd $node */

        return new BitwiseOr($node->var, $node->expr, $node->getAttributes());
    }
}
