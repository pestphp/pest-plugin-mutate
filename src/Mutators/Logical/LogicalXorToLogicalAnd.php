<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalXor;

class LogicalXorToLogicalAnd extends AbstractMutator
{
    public const string SET = 'Logical';

    public const string DESCRIPTION = 'Converts the logical xor operator to the logical and operator.';

    public const string DIFF = <<<'DIFF'
        if ($a xor $b) {  // [tl! remove]
        if ($a && $b) {  // [tl! add]
            // ...
        }
        DIFF;

    public static function nodesToHandle(): array
    {
        return [LogicalXor::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var LogicalXor $node */
        return new LogicalAnd($node->left, $node->right);
    }
}
