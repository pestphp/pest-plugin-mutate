<?php

declare(strict_types=1);

test('a mutant covered only by dataset keys that hold a class name runs its covering tests', function (): void {
    expect(mutationTestOutput('DatasetKeyNames'))
        ->toContain('Mutations: 3 untested, 1 tested');
});
