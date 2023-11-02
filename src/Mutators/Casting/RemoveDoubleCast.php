<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Casting;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\Cast\Double;

class RemoveDoubleCast implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Double::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Double;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\Cast\Double $node */

        return $node->expr;
    }
}
