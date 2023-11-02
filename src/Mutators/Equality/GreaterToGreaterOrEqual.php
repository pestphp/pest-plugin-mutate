<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;

class GreaterToGreaterOrEqual implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Greater::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Greater;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Greater $node */
        return new GreaterOrEqual($node->left, $node->right);
    }
}
