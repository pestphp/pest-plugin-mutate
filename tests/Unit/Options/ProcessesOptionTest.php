<?php

declare(strict_types=1);

use Pest\Mutate\Support\Configuration\CliConfiguration;

it('removes the processes option from forwarded test-run arguments when parallel is enabled', function (): void {
    $configuration = new CliConfiguration;

    $arguments = $configuration->fromArguments([
        'vendor/bin/pest',
        'tests',
        '--mutate',
        '--parallel',
        '--processes=2',
    ]);

    expect($configuration->toArray())
        ->parallel->toBeTrue()
        ->processes->toBe(2);

    expect($arguments)
        ->toContain('vendor/bin/pest')
        ->toContain('tests')
        ->not->toContain('--mutate')
        ->not->toContain('--parallel')
        ->not->toContain('--processes=2');
});
