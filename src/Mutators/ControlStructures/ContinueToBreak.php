<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Continue_;

class ContinueToBreak extends AbstractMutator
{
    public static function nodesToHandle(): array
    {
        return [Continue_::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Stmt\Continue_ $node */

        return new Break_($node->num, $node->getAttributes());
    }
}
