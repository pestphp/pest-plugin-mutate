<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Support\MutationTestResult;
use Symfony\Component\Finder\SplFileInfo;

class MutationTestCollection
{
    /**
     * @param  array<int, MutationTest>  $tests
     */
    public function __construct(
        public readonly SplFileInfo $file,
        private array $tests = [],
    ) {
    }

    public function add(MutationTest $test): void
    {
        $this->tests[] = $test;
    }

    /**
     * @return array<int, MutationTest>
     */
    public function tests(): array
    {
        return $this->tests;
    }

    public function count(): int
    {
        return count($this->tests);
    }

    public function survived(): int
    {
        return count(array_filter($this->tests, fn (MutationTest $test): bool => $test->result() === MutationTestResult::Survived));
    }

    public function killed(): int
    {
        return count(array_filter($this->tests, fn (MutationTest $test): bool => $test->result() === MutationTestResult::Killed));
    }

    public function timedOut(): int
    {
        return count(array_filter($this->tests, fn (MutationTest $test): bool => $test->result() === MutationTestResult::Timeout));
    }

    public function notCovered(): int
    {
        return count(array_filter($this->tests, fn (MutationTest $test): bool => $test->result() === MutationTestResult::NotCovered));
    }
}
