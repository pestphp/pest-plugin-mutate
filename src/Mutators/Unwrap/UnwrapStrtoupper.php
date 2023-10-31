<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Unwrap;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;

class UnwrapStrtoupper implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        if (! $node instanceof FuncCall) {
            return false;
        }

        return $node->name->getParts() === ['strtoupper']; // @phpstan-ignore-line
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\FuncCall $node */
        return $node->args[0]->value; // @phpstan-ignore-line
    }
}
