<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Assignment;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp\Mod;
use PhpParser\Node\Expr\AssignOp\Mul;

class ModulusEqualToMultiplyEqual extends AbstractMutator
{
    public const string SET = 'Assignment';

    public const string DESCRIPTION = 'Replaces `%=` with `*=`.';

    public const string DIFF = <<<'DIFF'
        $a %= $b;  // [tl! remove]
        $a *= $b;  // [tl! add]
        DIFF;

    public static function nodesToHandle(): array
    {
        return [Mod::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var Mod $node */
        return new Mul($node->var, $node->expr, $node->getAttributes());
    }
}
