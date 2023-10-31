<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;

class ConcatSwitchSides implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Concat;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Concat $node */
        $tmp = $node->left;
        $node->left = $node->right;
        $node->right = $tmp;

        return $node;
    }
}
