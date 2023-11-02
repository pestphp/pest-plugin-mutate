<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Foreach_;

class ForeachEmptyIterable implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Foreach_::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Foreach_;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Stmt\Foreach_ $node */
        $node->expr = new Array_(attributes: [
            'kind' => Array_::KIND_SHORT,
        ]);

        return $node;
    }
}
