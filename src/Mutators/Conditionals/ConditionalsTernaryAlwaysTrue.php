<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Conditionals;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Name;

class ConditionalsTernaryAlwaysTrue implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Ternary;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Ternary $node */
        $node->cond = new ConstFetch(new Name([0 => 'true']));

        return $node;
    }
}
