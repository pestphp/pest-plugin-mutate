<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;

class SmallerOrEqualToGreater extends AbstractMutator
{
    public const string SET = 'Equality';

    public const string DESCRIPTION = 'Converts the smaller or equal operator to the greater operator.';

    public const string DIFF = <<<'DIFF'
        if ($a <= $b) {  // [tl! remove]
        if ($a > $b) {  // [tl! add]
            // ...
        }
        DIFF;

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
