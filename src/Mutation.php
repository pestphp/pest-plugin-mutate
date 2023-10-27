<?php

declare(strict_types=1);

namespace Pest\Mutate;

use PhpParser\Node;
use Symfony\Component\Finder\SplFileInfo;

class Mutation
{
    /**
     * @param  array<array-key, Node>  $modifiedAst
     */
    public function __construct(
        public SplFileInfo $file,
        public string $mutator,
        public Node $originalNode,
        public ?Node $mutatedNode,
        public array $modifiedAst,
    ) {
    }

    /**
     * @return array{file: (string | false), mutator: string, originalNode: Node, mutatedNode: (Node | null), modifiedAst: array<array-key, Node>}
     */
    public function __serialize(): array
    {
        return [
            'file' => $this->file->getRealPath(),
            'mutator' => $this->mutator,
            'originalNode' => $this->originalNode,
            'mutatedNode' => $this->mutatedNode,
            'modifiedAst' => $this->modifiedAst,
        ];
    }

    /**
     * @param  array{file: string, mutator: string, originalNode: Node, mutatedNode: (Node | null), modifiedAst: array<array-key, Node>}  $data
     */
    public function __unserialize(array $data): void
    {
        $this->file = new SplFileInfo($data['file'], '', '');
        $this->mutator = $data['mutator'];
        $this->originalNode = $data['originalNode'];
        $this->mutatedNode = $data['mutatedNode'];
        $this->modifiedAst = $data['modifiedAst'];
    }
}
