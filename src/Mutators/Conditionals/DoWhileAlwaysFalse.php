<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Conditionals;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Do_;

class DoWhileAlwaysFalse implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Do_;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Stmt\Do_ $node */
        $node->cond = new ConstFetch(new Name([0 => 'false']));

        return $node;
    }
}
