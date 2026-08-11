<?php

declare(strict_types=1);

namespace Pest\Mutate\Repositories;

use Pest\Mutate\Mutation;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;
use Pest\Mutate\Support\ResultCache;

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

    public function untested(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->untested(), $this->tests));
    }

    public function tested(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->tested(), $this->tests));
    }

    public function timedOut(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->timedOut(), $this->tests));
    }

    public function uncovered(): int
    {
        return array_sum(array_map(fn (MutationTestCollection $testCollection): int => $testCollection->uncovered(), $this->tests));
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

        return ($this->tested() + $this->timedOut()) / $this->total() * 100;
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

    /**
     * Returns the shard units, one per mutated file.
     *
     * A file is the unit of mutation work: every mutation belongs to exactly one, and
     * the tests covering it must run in the same shard for the result to be honest.
     * Sharding test classes instead would regenerate a file's mutations in every shard
     * holding one of its covering tests, and report the ones killed elsewhere as escaped.
     *
     * @return array<string, array{time: float, tests: list<string>}>
     */
    public function units(string $rootPath): array
    {
        $units = [];

        foreach ($this->tests as $file => $testCollection) {
            $time = 0.0;
            $tests = [];

            foreach ($testCollection->tests() as $test) {
                $time += $test->duration();

                foreach ($test->coveringTestClasses() as $class) {
                    $tests[$class] = true;
                }
            }

            if ($tests === []) {
                continue;
            }

            $units[$this->relativePath($file, $rootPath)] = [
                'time' => round($time, 4),
                'tests' => array_keys($tests),
            ];
        }

        return $units;
    }

    /**
     * Makes a mutated file's path relative to the root, so the units survive being
     * written on one machine and read on another.
     */
    private function relativePath(string $file, string $rootPath): string
    {
        $prefix = rtrim($rootPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return str_starts_with($file, $prefix) ? substr($file, strlen($prefix)) : $file;
    }

    public function sortByEscapedFirst(): void
    {
        uasort($this->tests, fn (MutationTestCollection $a, MutationTestCollection $b): int => $b->hasLastRunEscapedMutation() <=> $a->hasLastRunEscapedMutation());

        foreach ($this->tests as $testCollection) {
            $testCollection->sortByEscapedFirst();
        }
    }

    public function saveResults(): void
    {
        foreach ($this->tests as $testCollection) {
            ResultCache::instance()->put($testCollection);
        }
    }
}
