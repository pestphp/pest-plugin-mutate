<?php

declare(strict_types=1);

namespace Pest\Mutate\Repositories;

use Pest\Mutate\Mutation;
use Pest\Mutate\MutationTest;
use Pest\Mutate\Support\MutationTestResult;

class MutationRepository
{
    /**
     * Holds the mutation tests per file.
     *
     * @var array<string, array<int, MutationTest>>
     */
    private array $tests = [];

    public function add(Mutation $mutation): void
    {
        $this->tests[$mutation->file->getRealPath()][] = new MutationTest($mutation);
    }

    /**
     * @return array<string, array<int, MutationTest>>
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
        return array_sum(array_map(fn (array $mutations): int => count($mutations), $this->tests));
    }

    public function survived(): int
    {
        return count($this->testsByResult(MutationTestResult::Survived));
    }

    public function killed(): int
    {
        return count($this->testsByResult(MutationTestResult::Killed));
    }

    public function timedOut(): int
    {
        return count($this->testsByResult(MutationTestResult::Timeout));
    }

    public function notCovered(): int
    {
        return count($this->testsByResult(MutationTestResult::NotCovered));
    }

    /**
     * @return array<int, MutationTest>
     */
    private function testsByResult(MutationTestResult $result): array
    {
        return array_merge(...array_values(array_map(fn (array $tests): array => array_filter($tests, fn (MutationTest $test): bool => $test->result() === $result), $this->tests)));
    }
}
