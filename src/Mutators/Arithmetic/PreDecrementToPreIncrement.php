<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;

class PreDecrementToPreIncrement implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof PreDec;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\PreDec $node */

        return new PreInc($node->var, $node->getAttributes());
    }
}
