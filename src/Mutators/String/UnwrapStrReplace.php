<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;
use PhpParser\Node;

class UnwrapStrReplace extends AbstractFunctionCallUnwrapMutator
{
    public static function functionName(): string
    {
        return 'str_replace';
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\FuncCall $node */
        return $node->args[2]->value; // @phpstan-ignore-line
    }
}
