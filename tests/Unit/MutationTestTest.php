<?php

declare(strict_types=1);

use Pest\Mutate\Mutation;
use Pest\Mutate\MutationTest;
use Symfony\Component\Finder\SplFileInfo;

it('reads the fully qualified test class out of a coverage identifier', function (string $identifier, string $expected): void {
    $test = new MutationTest(new Mutation(new SplFileInfo(__FILE__, '', ''), 'id', 'SomeMutator', 1, 2, '', ''));

    $method = new ReflectionClass($test)->getMethod('testClass');

    expect($method->invoke($test, $identifier))->toBe($expected);
})->with([
    'pest test' => ['P\Tests\Unit\FooTest::__pest_evaluable_it_works', 'Tests\Unit\FooTest'],
    'phpunit test' => ['Tests\Unit\FooTest::test_bar', 'Tests\Unit\FooTest'],
    'dataset' => ['P\Tests\Unit\FooTest::__pest_evaluable_it_works#0', 'Tests\Unit\FooTest'],
    'unnamespaced' => ['P\FooTest::test_bar', 'FooTest'],
    'no separator' => ['Tests\Unit\FooTest', 'Tests\Unit\FooTest'],
]);

it('has no covering test classes before it starts', function (): void {
    $test = new MutationTest(new Mutation(new SplFileInfo(__FILE__, '', ''), 'id', 'SomeMutator', 1, 2, '', ''));

    expect($test->coveringTestClasses())->toBe([]);
});
