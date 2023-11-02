<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;

class EqualToIdentical implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [Equal::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof Equal;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\Equal $node */
        return new Identical($node->left, $node->right, $node->getAttributes());
    }
}
