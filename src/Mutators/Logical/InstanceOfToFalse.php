<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Name;

class InstanceOfToFalse implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Instanceof_::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Instanceof_;
    }

    public static function mutate(Node $node): Node
    {
        return new ConstFetch(new Name('false'));
    }
}
