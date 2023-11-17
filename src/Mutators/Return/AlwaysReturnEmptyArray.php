<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Return;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Return_;

class AlwaysReturnEmptyArray extends AbstractMutator
{
    public const SET = 'Return';

    public const DESCRIPTION = 'Mutates a return statement to an empty array';

    public const DIFF = <<<'DIFF'
        return [1];  // [tl! remove]
        return [];  // [tl! add]
        DIFF;

    public static function nodesToHandle(): array
    {
        return [Return_::class];
    }

    public static function can(Node $node): bool
    {
        if (! $node instanceof Return_) {
            return false;
        }

        $parent = $node->getAttribute('parent');

        if (! $parent instanceof Function_) {
            return false;
        }

        if ($node->expr instanceof Array_ && $node->expr->items === []) {
            return false;
        }

        return $parent->returnType instanceof Identifier &&
            $parent->returnType->name === 'array';
    }

    public static function mutate(Node $node): Node
    {
        /** @var Return_ $node */
        $node->expr->items = []; // @phpstan-ignore-line

        return $node;
    }
}
