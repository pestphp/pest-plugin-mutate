<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;

class SmallerToSmallerOrEqual implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Smaller::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Smaller;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Smaller $node */
        return new SmallerOrEqual($node->left, $node->right);
    }
}
