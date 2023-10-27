<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class NodeVisitor extends NodeVisitorAbstract
{
    private int $nodeCount = 0;

    public function __construct(private readonly string $mutator, private readonly MutationGenerator $generator)
    {
    }

    public function leaveNode(Node $node): Node|int|null
    {
        if ($this->generator->hasMutated()) {
            return NodeTraverser::STOP_TRAVERSAL;
        }

        $this->nodeCount++;

        if ($this->nodeCount <= MutationGenerator::$lastMutatedNodeCount) {
            return null;
        }

        if ($this->mutator::can($node)) {
            $this->generator->trackMutation();
            MutationGenerator::$lastMutatedNodeOriginal = clone $node;
            MutationGenerator::$lastMutatedNodeCount = $this->nodeCount;
            MutationGenerator::$lastMutatedNode = $mutatedNode = $this->mutator::mutate($node);

            return $mutatedNode;
        }

        return null;
    }
}
