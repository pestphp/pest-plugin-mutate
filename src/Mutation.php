<?php

declare(strict_types=1);

namespace Pest\Mutate;

use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Finder\SplFileInfo;

class Mutation
{
    /**
     * @param  array<array-key, Node>  $modifiedAst
     */
    public function __construct(
        public readonly SplFileInfo $file,
        public readonly string $mutator,
        public readonly Node $originalNode,
        public readonly ?Node $modifiedNode,
        public readonly array $modifiedAst,
    ) {
    }

    public function modifiedSource(): string
    {
        // TODO: resolve from container
        $prettyPrinter = new Standard();

        return $prettyPrinter->prettyPrintFile($this->modifiedAst);
    }

    /**
     * @return array{original: string[], modified: string[]}
     */
    public function diff(): array
    {
        $prettyPrinter = new Standard();

        $original = explode(PHP_EOL, htmlentities($prettyPrinter->prettyPrintFile([$this->originalNode])));
        $modified = explode(PHP_EOL, htmlentities($prettyPrinter->prettyPrintFile([$this->modifiedNode]))); // @phpstan-ignore-line

        return [
            'original' => array_slice($original, 2),
            'modified' => array_slice($modified, 2),
        ];
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
            'mutatedNode' => $this->modifiedNode,
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
        $this->modifiedNode = $data['mutatedNode'];
        $this->modifiedAst = $data['modifiedAst'];
    }
}
