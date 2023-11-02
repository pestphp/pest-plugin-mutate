<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Coalesce;

class CoalesceRemoveLeft implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Coalesce::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Coalesce;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Coalesce $node */

        return $node->right;
    }
}
