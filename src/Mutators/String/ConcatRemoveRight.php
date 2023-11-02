<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;

class ConcatRemoveRight implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Concat::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Concat;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Concat $node */

        return $node->left;
    }
}
