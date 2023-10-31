<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Casting;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\Cast\Object_;

class RemoveObjectCast implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Object_;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\Cast\Object_ $node */

        return $node->expr;
    }
}
