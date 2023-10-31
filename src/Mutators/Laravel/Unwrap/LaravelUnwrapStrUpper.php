<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Laravel\Unwrap;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;

// TODO: This is a POC, lot of refactor and extraction needed
class LaravelUnwrapStrUpper implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        if (! $node instanceof MethodCall && ! $node instanceof StaticCall) {
            return false;
        }

        if ($node->name->name !== 'upper') { // @phpstan-ignore-line
            return false;
        }

        if ($node instanceof StaticCall) {
            $fullyQualified = $node->class->getAttribute('resolvedName');
            if ($fullyQualified instanceof FullyQualified && $fullyQualified->toCodeString() === '\Illuminate\Support\Str') {
                return true;
            }
        }

        return true;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\StaticCall $node */
        return $node->args[0]->value; // @phpstan-ignore-line
    }
}
