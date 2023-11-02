<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Number;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\DeclareDeclare;

class DecrementInteger implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof LNumber &&
            $node->value < PHP_INT_MAX &&
            ! $node->getAttribute('parent') instanceof DeclareDeclare;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Scalar\LNumber $node */
        $node->value -= $node->getAttribute('parent') instanceof UnaryMinus ? -1 : 1;

        return $node;
    }
}
