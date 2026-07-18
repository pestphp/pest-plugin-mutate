<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\ControlStructures;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Continue_;

class ContinueToBreak extends AbstractMutator
{
    public const string SET = 'ControlStructures';

    public const string DESCRIPTION = 'Replaces `continue` with `break`.';

    public const string DIFF = <<<'DIFF'
        foreach ($items as $item) {
            if ($item === 'foo') {
                continue;  // [tl! remove]
                break;  // [tl! add]
            }
        }
        DIFF;

    public static function nodesToHandle(): array
    {
        return [Continue_::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Continue_ $node */
        return new Break_($node->num, $node->getAttributes());
    }
}
