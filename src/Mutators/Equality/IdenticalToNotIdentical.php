<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;

class IdenticalToNotIdentical implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Identical;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Identical $node */
        return new NotIdentical($node->left, $node->right, $node->getAttributes());
    }
}
