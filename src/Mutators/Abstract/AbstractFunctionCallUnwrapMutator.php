<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Abstract;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\VariadicPlaceholder;

abstract class AbstractFunctionCallUnwrapMutator extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [FuncCall::class];
    }

    #[\Override]
    public static function can(Node $node): bool
    {
        if (! $node instanceof FuncCall) {
            return false;
        }

        if (! $node->name instanceof Name) {
            return false;
        }

        if ($node->name->getParts() !== [static::functionName()]) {
            return false;
        }

        return $node->isFirstClassCallable() || ($node->args[0] ?? null) instanceof Arg;
    }

    public static function mutate(Node $node): Node
    {
        assert($node instanceof FuncCall);

        $argument = $node->args[0] ?? null;

        if ($argument instanceof VariadicPlaceholder) {
            return new ArrowFunction([
                'params' => [new Param(new Variable('value'))],
                'expr' => new Variable('value'),
            ]);
        }

        assert($argument instanceof Arg);

        return $argument->value;
    }

    abstract public static function functionName(): string;
}
