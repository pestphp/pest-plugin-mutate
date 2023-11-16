<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Exceptions\ShouldNotHappen;
use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Finder\SplFileInfo;

class Mutation
{
    private const TMP_FOLDER = __DIR__
    .DIRECTORY_SEPARATOR
    .'..'
    .DIRECTORY_SEPARATOR
    .'.temp'
    .DIRECTORY_SEPARATOR
    .'mutations';

    /**
     * @param  array{original: string[], modified: string[]}  $diff
     */
    public function __construct(
        public readonly SplFileInfo $file,
        public readonly string $mutator,
        public readonly int $startLine,
        public readonly int $endLine,
        public readonly array $diff,
        public readonly string $modifiedSourcePath,
    ) {
    }

    /**
     * @param  array<array-key, Node>  $modifiedAst
     */
    public static function create(
        SplFileInfo $file,
        string $mutator,
        Node $originalNode,
        ?Node $modifiedNode,
        array $modifiedAst,
    ): self {
        $modifiedSource = (new Standard())->prettyPrintFile($modifiedAst);
        $modifiedSourcePath = self::TMP_FOLDER.DIRECTORY_SEPARATOR.hash('xxh3', $modifiedSource).'.php';
        file_put_contents($modifiedSourcePath, $modifiedSource);

        return new self(
            $file,
            $mutator,
            $originalNode->getStartLine(),
            $originalNode->getEndLine(),
            self::diff($originalNode, $modifiedNode),
            $modifiedSourcePath,
        );
    }

    public function modifiedSource(): string
    {
        $source = file_get_contents($this->modifiedSourcePath);

        if ($source === false) {
            throw ShouldNotHappen::fromMessage('Unable to read modified source file.');
        }

        return $source;
    }

    /**
     * @return array{original: string[], modified: string[]}
     */
    private static function diff(Node $originalNode, ?Node $modifiedNode): array
    {
        $prettyPrinter = new Standard();

        $original = explode(PHP_EOL, htmlentities($prettyPrinter->prettyPrintFile([$originalNode])));
        $modified = explode(PHP_EOL, htmlentities($prettyPrinter->prettyPrintFile($modifiedNode instanceof Node ? [$modifiedNode] : [])));

        return [
            'original' => array_slice($original, 2),
            'modified' => array_slice($modified, 2),
        ];
    }

    /**
     * @return array{file: string, mutator: string, start_line: int, end_line: int, diff: array{original: string[], modified: string[]}, modified_source_path: string}
     */
    public function __serialize(): array
    {
        return [
            'file' => $this->file->getRealPath(),
            'mutator' => $this->mutator,
            'start_line' => $this->startLine,
            'end_line' => $this->endLine,
            'diff' => $this->diff,
            'modified_source_path' => $this->modifiedSourcePath,
        ];
    }

    /**
     * @param  array{file: string, mutator: string, start_line: int, end_line: int, diff: array{original: string[], modified: string[]}, modified_source_path: string}  $data
     */
    public function __unserialize(array $data): void
    {
        $this->file = new SplFileInfo($data['file'], '', '');
        $this->mutator = $data['mutator'];
        $this->startLine = $data['start_line'];
        $this->endLine = $data['end_line'];
        $this->diff = $data['diff'];
        $this->modifiedSourcePath = $data['modified_source_path'];
    }
}
