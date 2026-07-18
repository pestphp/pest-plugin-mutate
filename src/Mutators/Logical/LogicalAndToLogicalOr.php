<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;

class LogicalAndToLogicalOr extends AbstractMutator
{
    public const string SET = 'Logical';

    public const string DESCRIPTION = 'Converts the logical and operator to the logical or operator.';

    public const string DIFF = <<<'DIFF'
        if ($a && $b) {  // [tl! remove]
        if ($a || $b) {  // [tl! add]
            // ...
        }
        DIFF;

    public static function nodesToHandle(): array
    {
        return [LogicalAnd::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var LogicalAnd $node */
        return new LogicalOr($node->left, $node->right);
    }
}
