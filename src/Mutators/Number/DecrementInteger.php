<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Number;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Scalar\LNumber;

class DecrementInteger implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof LNumber;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Scalar\LNumber $node */
        $node->value--;

        return $node;
    }
}