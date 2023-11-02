<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp\Concat;

class ConcatEqualToEqual implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Concat::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Concat;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\AssignOp\Concat $node */

        return new Assign($node->var, $node->expr, $node->getAttributes());
    }
}
