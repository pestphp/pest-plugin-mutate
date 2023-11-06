<?php

declare(strict_types=1);

namespace Pest\Mutate\Repositories;

use Pest\Mutate\Mutation;
use Pest\Mutate\Support\MutationTestResult;

class MutationRepository
{
    /**
     * Holds the mutations per file.
     *
     * @var array<string, array<int, Mutation>>
     */
    private array $mutations = [];

    public function add(Mutation $mutation): void
    {
        $this->mutations[$mutation->file->getRealPath()][] = $mutation;
    }

    /**
     * @return array<string, array<int, Mutation>>
     */
    public function all(): array
    {
        return $this->mutations;
    }

    public function count(): int
    {
        return count($this->mutations);
    }

    public function total(): int
    {
        return array_sum(array_map(fn (array $mutations): int => count($mutations), $this->mutations));
    }

    public function survived(): int
    {
        return count($this->mutationsByResult(MutationTestResult::Survived));
    }

    public function killed(): int
    {
        return count($this->mutationsByResult(MutationTestResult::Killed));
    }

    public function timedOut(): int
    {
        return count($this->mutationsByResult(MutationTestResult::Timeout));
    }

    public function notCovered(): int
    {
        return count($this->mutationsByResult(MutationTestResult::NotCovered));
    }

    /**
     * @return array<int, Mutation>
     */
    private function mutationsByResult(MutationTestResult $result): array
    {
        return array_merge(...array_values(array_map(fn (array $mutations): array => array_filter($mutations, fn (Mutation $mutation): bool => $mutation->result === $result), $this->mutations)));
    }
}
