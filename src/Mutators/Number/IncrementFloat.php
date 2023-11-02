<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Number;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Scalar\DNumber;

class IncrementFloat extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [DNumber::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Scalar\LNumber $node */
        $node->value++;

        return $node;
    }
}
