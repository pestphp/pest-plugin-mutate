<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;
use PhpParser\Node;

class UnwrapArrayMap extends AbstractFunctionCallUnwrapMutator
{
    public static function functionName(): string
    {
        return 'array_map';
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\FuncCall $node */
        return $node->args[1]->value; // @phpstan-ignore-line
    }
}
