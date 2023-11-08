<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;

class NullSafePropertyCallToPropertyCall extends AbstractMutator
{
    public const SET = 'Logical';

    public const DESCRIPTION = 'Converts nullsafe property call to property call.';

    public const DIFF = <<<'DIFF'
        $a?->b;  // [tl! remove]
        $a->b;  // [tl! add]
        DIFF;

    public static function nodesToHandle(): array
    {
        return [NullsafePropertyFetch::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\NullsafePropertyFetch $node */

        return new PropertyFetch($node->var, $node->name, $node->getAttributes());
    }
}
