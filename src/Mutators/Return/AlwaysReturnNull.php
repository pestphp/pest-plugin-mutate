<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Return;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Return_;

class AlwaysReturnNull extends AbstractMutator
{
    public const SET = 'Return';

    public const DESCRIPTION = 'Mutates a return statement to null if it is not null';

    public const DIFF = <<<'DIFF'
        return $a;  // [tl! remove]
        return null;  // [tl! add]
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

        if ($node->expr instanceof ConstFetch && $node->expr->name->parts[0] === 'null') {
            return false;
        }

        return $parent->returnType === null ||
            $parent->returnType->getType() === 'NullableType';
    }

    public static function mutate(Node $node): Node
    {
        /** @var Return_ $node */
        $node->expr = new ConstFetch(new Name('null'));

        return $node;
    }
}
