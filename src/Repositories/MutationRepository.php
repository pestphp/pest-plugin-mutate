<?php

declare(strict_types=1);

namespace Pest\Mutate\Repositories;

use Pest\Mutate\Mutation;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;

class MutationRepository
{
    /**
     * Holds the mutation tests per file.
     *
     * @var array<string, MutationTestCollection>
     */
    private array $tests = [];

    public function add(Mutation $mutation): void
    {
        if (! isset($this->tests[$mutation->file->getRealPath()])) {
            $this->tests[$mutation->file->getRealPath()] = new MutationTestCollection($mutation->file);
        }

        $test = new MutationTest($mutation);
        $this->tests[$mutation->file->getRealPath()]->add($test);
    }

    /**
     * @return array<string, MutationTestCollection>
     */
    public function all(): array
    {
        return $this->tests;
    }

    public function count(): int
    {
        return count($this->tests);
    }

    public function total(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->count(), $this->tests));
    }

    public function survived(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->survived(), $this->tests));
    }

    public function killed(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->killed(), $this->tests));
    }

    public function timedOut(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->timedOut(), $this->tests));
    }

    public function notCovered(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->notCovered(), $this->tests));
    }

    public function notRun(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->notRun(), $this->tests));
    }

    public function score(): float
    {
        if ($this->total() === 0) {
            return 0;
        }

        return ($this->killed() + $this->timedOut()) / $this->total() * 100;
    }

    /**
     * @return array<int, MutationTest>
     */
    public function slowest(): array
    {
        $allTests = array_merge(...array_values(array_map(fn (MutationTestCollection $testCollection): array => $testCollection->tests(), $this->tests)));

        $allTests = array_filter($allTests, fn (MutationTest $test): bool => $test->duration() > 0);

        usort($allTests, fn (MutationTest $a, MutationTest $b): int => $b->duration() <=> $a->duration());

        return array_slice($allTests, 0, 10);
    }
}
