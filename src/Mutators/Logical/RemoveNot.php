<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;

class RemoveNot extends AbstractMutator
{
    public const string SET = 'Logical';

    public const string DESCRIPTION = 'Removes the not operator.';

    public const string DIFF = <<<'DIFF'
        if (!$a) {  // [tl! remove]
        if ($a) {  // [tl! add]
            // ...
        }
        DIFF;

    public static function nodesToHandle(): array
    {
        return [BooleanNot::class];
    }

    public static function mutate(Node $node): Node
    {
        /** @var BooleanNot $node */
        return $node->expr;
    }
}
