<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class NodeVisitor extends NodeVisitorAbstract
{
    private int $nodeCount = 0;

    /**
     * @param  array<int, int>  $linesToMutate
     * @param  callable  $hasMutated
     * @param  callable  $trackMutation
     */
    public function __construct(
        private readonly string $mutator,
        private readonly int $offset,
        private readonly array $linesToMutate,
        private $hasMutated, // @pest-ignore-type
        private $trackMutation, // @pest-ignore-type
    ) {
    }

    public function leaveNode(Node $node): Node|int|null
    {
        if (($this->hasMutated)()) {
            return NodeTraverser::STOP_TRAVERSAL;
        }

        if ($this->nodeCount++ < $this->offset) {
            return null;
        }

        if ($this->linesToMutate !== [] && ! in_array($node->getStartLine(), $this->linesToMutate, true)) {
            return null;
        }

        if ($this->mutator::can($node)) {
            ($this->trackMutation)(
                $this->nodeCount,
                clone $node,
                $mutatedNode = $this->mutator::mutate($node),
            );

            return $mutatedNode;
        }

        return null;
    }
}
