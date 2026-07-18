<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Removal;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Nop;

class RemoveFunctionCall extends AbstractMutator
{
    public const string SET = 'Removal';

    public const string DESCRIPTION = 'Removes a function call';

    public const string DIFF = <<<'DIFF'
        foo();  // [tl! remove]
        DIFF;

    public static function nodesToHandle(): array
    {
        return [Expression::class];
    }

    #[\Override]
    public static function can(Node $node): bool
    {
        if (! $node instanceof Expression) {
            return false;
        }

        return $node->expr instanceof FuncCall;
    }

    public static function mutate(Node $node): Node
    {
        return new Nop;
    }
}
