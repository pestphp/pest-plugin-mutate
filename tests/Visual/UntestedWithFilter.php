<?php

declare(strict_types=1);

test('a run given --filter scores an escaped mutant as untested', function (string $filter): void {
    expect(mutationTestOutput('Untested', ...explode(' ', $filter)))
        ->toContain('Mutations: 3 untested, 1 tested')
        ->not->toContain('cannot be used more than once');
})->with([
    'equals form' => '--filter=escaped',
    'separate value' => '--filter escaped',
]);
