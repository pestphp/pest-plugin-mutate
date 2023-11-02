<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;

class PreIncrementToPreDecrement implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [PreDec::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof PreInc;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\PreInc $node */

        return new PreDec($node->var, $node->getAttributes());
    }
}
