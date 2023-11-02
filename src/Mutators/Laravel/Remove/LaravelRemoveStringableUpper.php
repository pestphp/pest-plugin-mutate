<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Laravel\Remove;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;

// TODO: This is a POC, lot of refactor and extraction needed
class LaravelRemoveStringableUpper implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [MethodCall::class];
    }

    public static function can(Node $node): bool
    {
        if (! $node instanceof MethodCall) {
            return false;
        }

        if ($node->name->name !== 'upper') { // @phpstan-ignore-line
            return false;
        }

        return self::parentIsStrCall($node);
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\MethodCall $node */
        return $node->var;
    }

    private static function parentIsStrCall(Node $node): bool
    {
        if ($node->var instanceof MethodCall) { // @phpstan-ignore-line
            return self::parentIsStrCall($node->var);
        }

        if ($node->var instanceof FuncCall) {
            if ($node->var->args === []) {
                return false;
            }
            $fullyQualified = $node->var->name->getAttribute('resolvedName');
            if ($fullyQualified instanceof FullyQualified && $fullyQualified->toCodeString() === '\str') {
                return true;
            }
        }

        if ($node->var instanceof StaticCall) {
            if ($node->var->name->name !== 'of') { // @phpstan-ignore-line
                return false;
            }
            $fullyQualified = $node->var->class->getAttribute('resolvedName');
            if ($fullyQualified instanceof FullyQualified && $fullyQualified->toCodeString() === '\Illuminate\Support\Str') {
                return true;
            }
        }

        return false;
    }
}
