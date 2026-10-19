<?php

declare(strict_types=1);

use Pest\Mutate\Support\MutantArguments;

it('keeps the arguments a mutant subprocess can accept', function (): void {
    expect(MutantArguments::from([
        'vendor/bin/pest',
        'tests/Unit',
        '--colors=never',
        '--configuration=phpunit.xml',
    ]))->toBe([
        'vendor/bin/pest',
        'tests/Unit',
        '--colors=never',
        '--configuration=phpunit.xml',
    ]);
});

it('drops the coverage arguments, since the mutant reuses the original coverage', function (): void {
    expect(MutantArguments::from([
        'vendor/bin/pest',
        '--coverage-php=/tmp/coverage.php',
        '--coverage',
        'tests/Unit',
    ]))->toBe(['vendor/bin/pest', 'tests/Unit']);
});

it('drops the path of a coverage option written as two arguments', function (): void {
    expect(MutantArguments::from([
        'vendor/bin/pest',
        '--coverage-php', '/tmp/coverage.php',
        '--coverage-text', '--colors=never',
        'tests/Unit',
    ]))->toBe(['vendor/bin/pest', '--colors=never', 'tests/Unit']);
});

it('drops the paratest only options, which phpunit would reject before running a test', function (string $argument): void {
    expect(MutantArguments::from(['vendor/bin/pest', $argument, 'tests/Unit']))
        ->toBe(['vendor/bin/pest', 'tests/Unit']);
})->with([
    '--functional',
    '--max-batch-size=10',
    '--max-processes=4',
    '--no-test-tokens',
    '--parallel',
    '--passthru-php=-d pcov.enabled=1',
    '--processes=2',
    '--runner=WrapperRunner',
    '--shard',
    '--shard=1/2',
    '--shard-test-distribution=sequential',
    '--shard-test-distribution-seed=0',
    '--tmp-dir=/tmp',
    '--verbose',
    '-m=10',
    '-p',
]);

it('drops the value of a paratest only option written as two arguments', function (): void {
    expect(MutantArguments::from([
        'vendor/bin/pest',
        '--processes', '2',
        '--passthru-php', '-d pcov.enabled=1',
        '--shard', '1/4',
        'tests/Unit',
    ]))->toBe(['vendor/bin/pest', 'tests/Unit']);
});

it('keeps the argument after a paratest only option that takes no value', function (): void {
    expect(MutantArguments::from(['vendor/bin/pest', '--functional', 'tests/Unit']))
        ->toBe(['vendor/bin/pest', 'tests/Unit']);
});

it('keeps an option whose name merely starts like a paratest only one', function (): void {
    expect(MutantArguments::from(['vendor/bin/pest', '--process-isolation', '--runner-up']))
        ->toBe(['vendor/bin/pest', '--process-isolation', '--runner-up']);
});

it('returns a list even when it drops an argument from the middle', function (): void {
    expect(array_keys(MutantArguments::from(['vendor/bin/pest', '--processes=2', 'tests/Unit'])))
        ->toBe([0, 1]);
});
