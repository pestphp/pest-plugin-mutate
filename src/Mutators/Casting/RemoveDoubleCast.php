<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Casting;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\Cast\Double;

class RemoveDoubleCast extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Double::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\Cast\Double $node */

        return $node->expr;
    }
}
