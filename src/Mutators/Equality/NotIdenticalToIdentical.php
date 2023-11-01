<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;

class NotIdenticalToIdentical implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof NotIdentical;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\NotIdentical $node */
        return new Identical($node->left, $node->right, $node->getAttributes());
    }
}
