<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;

class ConcatSwitchSides extends AbstractMutator
{
    public const SET = 'String';

    public const DESCRIPTION = 'Switches the sides of a concat expression.';

    public const DIFF = <<<'DIFF'
        $a = 'Hello' . ' World';  // [tl! remove]
        $a = ' World' . 'Hello';  // [tl! add]
        DIFF;

    public static function nodesToHandle(): array
    {
        return [Concat::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Concat $node */
        $tmp = $node->left;
        $node->left = $node->right;
        $node->right = $tmp;

        return $node;
    }
}
