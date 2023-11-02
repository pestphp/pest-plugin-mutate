<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Casting;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\Cast\Bool_;

class RemoveBooleanCast implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Bool_::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Bool_;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\Cast\Bool_ $node */

        return $node->expr;
    }
}
