<?php

declare(strict_types=1);

use Pest\Mutate\Tester\MutationTestRunner;

/**
 * @param  array<int|string, mixed>  $tests
 * @param  array<int, string>  $testIds
 * @param  array<int, string>  $expected
 */
it('resolves the tests covering a line from either code coverage shape', function (array $tests, array $testIds, array $expected): void {
    $runner = new MutationTestRunner;

    $method = new ReflectionClass($runner)->getMethod('testIdsCoveringLine');

    expect($method->invoke($runner, $tests, $testIds))->toBe($expected);
})->with([
    'ids in the values, up to php-code-coverage 14.2' => [
        ['P\Tests\Unit\FooTest::__pest_evaluable_it_works', 'Tests\Unit\BarTest::test_baz'],
        [],
        ['P\Tests\Unit\FooTest::__pest_evaluable_it_works', 'Tests\Unit\BarTest::test_baz'],
    ],
    'hit counts keyed by index, since php-code-coverage 14.3' => [
        [0 => 1, 2 => 4],
        [
            0 => 'P\Tests\Unit\FooTest::__pest_evaluable_it_works',
            1 => 'Tests\Unit\OtherTest::test_untouched',
            2 => 'Tests\Unit\BarTest::test_baz',
        ],
        ['P\Tests\Unit\FooTest::__pest_evaluable_it_works', 'Tests\Unit\BarTest::test_baz'],
    ],
    'an index with no test id is dropped' => [[7 => 1], [0 => 'Tests\Unit\FooTest::test_baz'], []],
    'nothing covers the line' => [[], [], []],
]);
