<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;

class TrueToFalse implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        if(!$node instanceof ConstFetch) {
            return false;
        }

        if($node->name->toCodeString() !== 'true') {
            return false;
        }

        $possibleFuncCall = $node->getAttribute('parent')?->getAttribute('parent');
        if(
            $possibleFuncCall instanceof Node\Expr\FuncCall &&
            in_array(
                $possibleFuncCall->name->toCodeString(),
                ['in_array', 'array_search'],
                true)
        ) {
            return false;
        }

        return true;
    }

    public static function mutate(Node $node): Node
    {
        return new ConstFetch(new Name('false'));
    }
}
