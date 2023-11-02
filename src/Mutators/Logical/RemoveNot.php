<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;

class RemoveNot implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [BooleanNot::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof BooleanNot;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BooleanNot $node */

        return $node->expr;
    }
}
