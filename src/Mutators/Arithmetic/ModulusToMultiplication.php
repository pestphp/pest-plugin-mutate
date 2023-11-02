<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Mod;
use PhpParser\Node\Expr\BinaryOp\Mul;

class ModulusToMultiplication extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Mod::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Mod $node */

        return new Mul($node->left, $node->right);
    }
}
