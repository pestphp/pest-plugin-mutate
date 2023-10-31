<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;

class PostDecrementToPostIncrement implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof PostDec;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\PostDec $node */

        return new PostInc($node->var, $node->getAttributes());
    }
}
