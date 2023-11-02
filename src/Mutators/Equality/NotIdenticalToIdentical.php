<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;

class NotIdenticalToIdentical extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [NotIdentical::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\NotIdentical $node */
        return new Identical($node->left, $node->right, $node->getAttributes());
    }
}
