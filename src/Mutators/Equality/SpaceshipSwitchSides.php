<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Spaceship;

class SpaceshipSwitchSides extends AbstractMutator
{
    public const string SET = 'Equality';

    public const string DESCRIPTION = 'Switches the sides of the spaceship operator.';

    public const string DIFF = <<<'DIFF'
        return $a <=> $b;  // [tl! remove]
        return $b <=> $a;  // [tl! add]
        DIFF;

    public static function nodesToHandle(): array
    {
        return [Spaceship::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Spaceship $node */
        $tmp = $node->left;
        $node->left = $node->right;
        $node->right = $tmp;

        return $node;
    }
}
