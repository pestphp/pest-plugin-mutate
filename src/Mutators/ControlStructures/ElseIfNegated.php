<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Stmt\ElseIf_;

class ElseIfNegated implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [ElseIf_::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof ElseIf_;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Stmt\ElseIf_ $node */
        $node->cond = new BooleanNot($node->cond);

        return $node;
    }
}
