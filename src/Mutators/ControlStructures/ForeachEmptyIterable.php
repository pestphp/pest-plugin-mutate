<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Foreach_;

class ForeachEmptyIterable extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Foreach_::class];
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
