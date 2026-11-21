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

        $argument = $node->args[0] ?? null;

        return $node->name->getParts() === [static::functionName()]
            && ($argument instanceof Arg || $argument instanceof VariadicPlaceholder);
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

        return $argument instanceof Arg ? $argument->value : $node;
    }

    abstract public static function functionName(): string;
}
