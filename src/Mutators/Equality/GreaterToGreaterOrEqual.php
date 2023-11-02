<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;

class GreaterToGreaterOrEqual extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Greater::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Greater $node */
        return new GreaterOrEqual($node->left, $node->right);
    }
}
