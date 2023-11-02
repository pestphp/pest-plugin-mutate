<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;

class GreaterOrEqualToGreater extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [GreaterOrEqual::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var GreaterOrEqual $node */
        return new Greater($node->left, $node->right);
    }
}
