<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use SebastianBergmann\Diff\Diff;
use SebastianBergmann\Diff\Parser;
use Symfony\Component\Process\Process;

class GitDiff
{
    private static ?self $instance = null;

    /**
     * @var ?array<int, Diff>
     */
    private ?array $diff = null;

    private ?string $branch = null;

    public static function getInstance(): self
    {
        if (! self::$instance instanceof \Pest\Mutate\Support\GitDiff) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function branch(string $branch): self
    {
        if ($this->branch === $branch) {
            return $this;
        }

        $this->branch = $branch;

        $this->diff = null;

        return $this;
    }

    public function uncommitted(): self
    {
        if ($this->branch === null) {
            return $this;
        }

        $this->branch = null;

        $this->diff = null;

        return $this;
    }

    /**
     * @return array<array-key, int>
     */
    public function modifiedLinesPerFile(string $file): array
    {
        $file = trim($file, '/');

        $diff = $this->getDiff();

        $diff = array_filter($diff, fn (Diff $diff): bool => $diff->getTo() === 'b/'.$file);

        if ($diff === []) {
            return [];
        }

        $diff = array_shift($diff);

        $linesNumbers = [];

        foreach ($diff->getChunks() as $chunk) {
            $lineNumber = $chunk->getStart();
            foreach ($chunk->getLines() as $line) {
                $lineNumber += $line->getType() === 2 ? -1 : 1;
                if ($line->getType() === 1) {
                    $linesNumbers[] = $lineNumber;
                }
            }
        }

        return $linesNumbers;
    }

    /**
     * @return array<int, Diff>
     */
    private function getDiff(): array
    {
        if ($this->diff !== null) {
            return $this->diff;
        }

        $command = $this->branch !== null ? ['git', 'diff', '--merge-base', $this->branch] : ['git', 'diff', 'HEAD'];

        $process = new Process($command);

        $output = $process->mustRun()
            ->getOutput();

        $this->diff = (new Parser())
            ->parse($output);

        return $this->diff;
    }
}
