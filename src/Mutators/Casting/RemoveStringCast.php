<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Casting;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\Cast\String_;

class RemoveStringCast extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [String_::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\Cast\String_ $node */

        return $node->expr;
    }
}
