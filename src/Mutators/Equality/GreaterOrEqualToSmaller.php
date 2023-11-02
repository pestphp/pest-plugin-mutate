<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\Smaller;

class GreaterOrEqualToSmaller extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [GreaterOrEqual::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var GreaterOrEqual $node */
        return new Smaller($node->left, $node->right);
    }
}
