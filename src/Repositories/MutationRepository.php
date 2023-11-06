<?php

declare(strict_types=1);

namespace Pest\Mutate\Repositories;

use Pest\Mutate\Mutation;

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
}
