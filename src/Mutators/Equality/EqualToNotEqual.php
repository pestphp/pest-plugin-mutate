<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\NotEqual;

class EqualToNotEqual implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Equal;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Equal $node */
        return new NotEqual($node->left, $node->right, $node->getAttributes());
    }
}
