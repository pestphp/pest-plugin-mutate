<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Equality;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Concerns\HasName;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;

class NotEqualToNotIdentical implements Mutator
{
    use HasName;

    public static function nodesToHandle(): array
    {
        return [NotIdentical::class];
    }

    public static function can(Node $node): bool
    {
        return $node instanceof NotEqual;
    }

    public static function mutate(Node $node): Node
    {
        /** @var Node\Expr\BinaryOp\NotEqual $node */
        return new NotIdentical($node->left, $node->right, $node->getAttributes());
    }
}
