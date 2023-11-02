<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\While_;

class WhileAlwaysFalse implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [While_::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof While_;
    }

    public static function mutate(Node $node): Node
    {
        /** @var While_ $node */
        $node->cond = new ConstFetch(new Name('false'));

        return $node;
    }
}
