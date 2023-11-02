<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Spaceship;

class SpaceshipSwitchSides implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Spaceship::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Spaceship;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Spaceship $node */
        $tmp = $node->left;
        $node->left = $node->right;
        $node->right = $tmp;

        return $node;
    }
}
