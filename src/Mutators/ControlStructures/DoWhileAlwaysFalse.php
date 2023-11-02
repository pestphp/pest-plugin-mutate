<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Do_;

class DoWhileAlwaysFalse extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Do_::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Stmt\Do_ $node */
        $node->cond = new ConstFetch(new Name('false'));

        return $node;
    }
}
