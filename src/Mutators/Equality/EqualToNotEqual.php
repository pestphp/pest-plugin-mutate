<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\NotEqual;

class EqualToNotEqual extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Equal::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Equal $node */
        return new NotEqual($node->left, $node->right, $node->getAttributes());
    }
}
