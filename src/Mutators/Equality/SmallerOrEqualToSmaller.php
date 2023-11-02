<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;

class SmallerOrEqualToSmaller extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [SmallerOrEqual::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var SmallerOrEqual $node */
        return new Smaller($node->left, $node->right);
    }
}
