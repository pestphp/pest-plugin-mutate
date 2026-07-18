<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Casting;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\Cast\Object_;

class RemoveObjectCast extends AbstractMutator
{
    public const string SET = 'Casting';

    public const string DESCRIPTION = 'Removes object cast.';

    public const string DIFF = <<<'DIFF'
        $a = (object) $b;  // [tl! remove]
        $a = $b;           // [tl! add]
        DIFF;

    public static function nodesToHandle(): array
    {
        return [Object_::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Object_ $node */
        return $node->expr;
    }
}
