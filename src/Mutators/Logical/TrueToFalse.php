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
        $possibleFuncCall = $node->getAttribute('parent')?->getAttribute('parent'); // @phpstan-ignore-line

        return ! ($possibleFuncCall instanceof FuncCall &&
            $possibleFuncCall->name instanceof Name &&
            in_array(
                $possibleFuncCall->name->toCodeString(),
                self::FUNCTIONS_TO_IGNORE,
                true)
        );
    }
}
