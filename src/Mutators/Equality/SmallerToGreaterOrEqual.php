<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\Smaller;

class SmallerToGreaterOrEqual implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Smaller;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Smaller $node */
        return new GreaterOrEqual($node->left, $node->right);
    }
}
