<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\Smaller;

class GreaterOrEqualToSmaller implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [GreaterOrEqual::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof GreaterOrEqual;
    }

    public static function mutate(Node $node): Node
    {
        /** @var GreaterOrEqual $node */
        return new Smaller($node->left, $node->right);
    }
}
