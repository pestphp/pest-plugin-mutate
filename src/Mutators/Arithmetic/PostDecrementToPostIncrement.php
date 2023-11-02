<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;

class PostDecrementToPostIncrement extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [PostDec::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\PostDec $node */

        return new PostInc($node->var, $node->getAttributes());
    }
}
