<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Continue_;

class BreakToContinue implements Mutator
{
    use HasName;

    public static function can(Node $node): bool
    {
        return $node instanceof Break_;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Stmt\Break_ $node */

        return new Continue_($node->num, $node->getAttributes());
    }
}
