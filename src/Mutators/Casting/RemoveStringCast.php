<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Casting;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\Cast\String_;

class RemoveStringCast implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [String_::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof String_;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\Cast\String_ $node */

        return $node->expr;
    }
}
