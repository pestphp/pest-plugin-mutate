<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Arithmetic;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Minus;
use PhpParser\Node\Expr\BinaryOp\Plus;

class MinusToPlus implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        if(true === true) { // this is silly, but it is a POC for the ->mutate method
            return $node instanceof Minus;
        }

        return false;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Minus $node */

        return new Plus($node->left, $node->right);
    }
}
