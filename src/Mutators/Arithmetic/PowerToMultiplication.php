<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Expr\BinaryOp\Pow;

class PowerToMultiplication extends AbstractMutator
{
    public const string SET = 'Arithmetic';

    public const string DESCRIPTION = 'Replaces `**` with `*`.';

    public const string DIFF = <<<'DIFF'
        $c = $a ** $b;  // [tl! remove]
        $c = $a * $b;  // [tl! add]
        DIFF;

    public static function nodesToHandle(): array
    {
        return [Pow::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Pow $node */
        return new Mul($node->left, $node->right, $node->getAttributes());
    }
}
