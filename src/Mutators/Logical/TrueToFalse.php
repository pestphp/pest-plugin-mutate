<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;

class TrueToFalse implements Mutator
{
    use HasName;

    private const FUNCTIONS_TO_IGNORE = ['in_array', 'array_search'];

    public static function nodesToHandle(): array
    {
        return [ConstFetch::class];
    }

    public static function can(Node $node): bool
    {
        if (! $node instanceof ConstFetch) {
            return false;
        }

        if ($node->name->toCodeString() !== 'true') {
            return false;
        }

        return self::isNotOnFunctionToIgnore($node);
    }

    public static function mutate(Node $node): Node
    {
        return new ConstFetch(new Name('false'));
    }

    private static function isNotOnFunctionToIgnore(ConstFetch $node): bool
    {
        $possibleFuncCall = $node->getAttribute('parent')->getAttribute('parent'); // @phpstan-ignore-line

        if (! $possibleFuncCall instanceof FuncCall) { // @pest-mutate-ignore: InstanceOfToTrue
            return true;
        }

        if (! $possibleFuncCall->name instanceof Name) {
            return true;
        }

        return ! in_array($possibleFuncCall->name->toCodeString(), self::FUNCTIONS_TO_IGNORE, true);
    }
}
