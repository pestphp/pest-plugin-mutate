<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Logical;

use Pest\Mutate\Mutators\Abstract\AbstractMutator;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Name;

class InstanceOfToTrue extends AbstractMutator
{
    public const string SET = 'Logical';

    public const string DESCRIPTION = 'Converts `instanceof` to `true`.';

    public const string DIFF = <<<'DIFF'
        if ($a instanceof $b) {  // [tl! remove]
        if (true) {  // [tl! add]
            // ...
        }
        DIFF;

    public static function nodesToHandle(): array
    {
        return [Instanceof_::class];
    }

    public static function mutate(Node $node): Node
    {
        return new ConstFetch(new Name('true'));
    }
}
