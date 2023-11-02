<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Casting;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\Cast\Int_;

class RemoveIntegerCast extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Int_::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\Cast\Int_ $node */

        return $node->expr;
    }
}
