<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;

class PostIncrementToPostDecrement extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [PostInc::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\PostInc $node */

        return new PostDec($node->var, $node->getAttributes());
    }
}
