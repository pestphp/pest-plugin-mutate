<?php

declare(strict_types=1);

test('a run given --filter scores an escaped mutant as untested', function (array $filter): void {
    expect(mutationTestOutput('Untested', ...$filter))
        ->toContain('Mutations: 3 untested, 1 tested');
})->with([
    'equals form' => [['--filter=escaped']],
    'separate value' => [['--filter', 'escaped']],
    'abbreviated' => [['--fil=escaped']],
    'empty value then separate value' => [['--filter=', 'escaped']],
    'blank value then separate value' => [['--filter= ', 'escaped']],
    'second equals sign then separate value' => [['--filter==ignored', 'escaped']],
    'value that starts with --coverage' => [['--coverage', '--filter', '--coverage|escaped', '--exclude-filter', 'does-not-match']],
]);

test('a mutant runs only the tests of the class and name that cover it', function (): void {
    expect(mutationTestOutput('SimilarNames', '--filter=Unit\\\\SizeTest::it checks size$'))
        ->toContain('Mutations: 3 untested, 1 tested');
});

test('a mutant runs only the class that covers it, not a class whose name ends with it', function (): void {
    expect(mutationTestOutput('SharedSuffix', '--filter=^Tests\\\\tests\\\\SharedSuffix\\\\SizeTest::'))
        ->toContain('Mutations: 3 untested, 1 tested');
});

test('a mutant covered by many tests of one class runs them', function (): void {
    expect(mutationTestOutput('ManyCoveringTests'))
        ->toContain('Mutations: 3 untested, 1 tested');
});

test('a mutant covered by many selected tests runs only those tests', function (): void {
    expect(mutationTestOutput('ManyCoveringTestsAndOneExcluded', '--filter=renews'))
        ->toContain('Mutations: 3 untested, 1 tested');
});
