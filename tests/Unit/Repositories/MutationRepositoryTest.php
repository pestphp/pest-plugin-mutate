<?php

declare(strict_types=1);

use Pest\Mutate\Mutation;
use Pest\Mutate\MutationTest;
use Pest\Mutate\Repositories\MutationRepository;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @param  array<int, string>  $coveringTestClasses
 */
$addMutation = function (MutationRepository $repository, string $id, float $duration, array $coveringTestClasses): void {
    $file = new SplFileInfo(__FILE__, '', '');

    $repository->add(new Mutation($file, $id, 'SomeMutator', 1, 2, '', ''));

    $tests = $repository->all()[$file->getRealPath()]->tests();
    $test = end($tests);

    assert($test instanceof MutationTest);

    $reflection = new ReflectionClass($test);
    $reflection->getProperty('start')->setValue($test, 0.0);
    $reflection->getProperty('finish')->setValue($test, $duration);
    $reflection->getProperty('coveringTestClasses')->setValue($test, $coveringTestClasses);
};

describe('units', function () use ($addMutation): void {
    it('groups the mutations of a file into one unit, with every test covering it', function () use ($addMutation): void {
        $repository = new MutationRepository;

        $addMutation($repository, 'a', 2.0, ['Tests\\Unit\\FooTest', 'Tests\\Unit\\BarTest']);
        $addMutation($repository, 'b', 0.5, ['Tests\\Unit\\FooTest']);

        expect($repository->units(dirname(__DIR__, 3)))->toBe([
            'tests/Unit/Repositories/MutationRepositoryTest.php' => [
                'time' => 2.5,
                'tests' => ['Tests\\Unit\\FooTest', 'Tests\\Unit\\BarTest'],
            ],
        ]);
    });

    it('leaves out a file no test covers', function () use ($addMutation): void {
        $repository = new MutationRepository;

        $addMutation($repository, 'a', 0.0, []);

        expect($repository->units(dirname(__DIR__, 3)))->toBe([]);
    });

    it('keeps an absolute path when the file is outside the root', function () use ($addMutation): void {
        $repository = new MutationRepository;

        $addMutation($repository, 'a', 1.0, ['Tests\\Unit\\FooTest']);

        expect(array_keys($repository->units('/somewhere/else')))->toBe([__FILE__]);
    });

    it('returns nothing when no mutation ran', function (): void {
        expect(new MutationRepository()->units('/'))->toBe([]);
    });
});
