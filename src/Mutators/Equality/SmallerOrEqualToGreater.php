<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;

class SmallerOrEqualToGreater extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [SmallerOrEqual::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var SmallerOrEqual $node */
        return new Greater($node->left, $node->right);
    }
}
