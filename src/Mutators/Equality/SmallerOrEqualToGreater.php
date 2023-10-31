<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;

class SmallerOrEqualToGreater implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof SmallerOrEqual;
    }

    public static function mutate(Node $node): Node
    {
        /** @var SmallerOrEqual $node */
        return new Greater($node->left, $node->right);
    }
}
