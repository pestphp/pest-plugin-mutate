<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Abstract;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;

abstract class AbstractFunctionCallUnwrapMutator implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        if (! $node instanceof FuncCall) { // @pest-mutate-ignore: InstanceOfToTrue
            return false;
        }

        if (! $node->name instanceof Name) {
            return false;
        }

        return $node->name->getParts() === [static::functionName()];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\FuncCall $node */
        return $node->args[0]->value; // @phpstan-ignore-line
    }

    abstract public static function functionName(): string;
}
