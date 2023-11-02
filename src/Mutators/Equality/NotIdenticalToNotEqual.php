<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;

class NotIdenticalToNotEqual extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [NotIdentical::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\NotIdentical $node */
        return new NotEqual($node->left, $node->right, $node->getAttributes());
    }
}
