<?php

declare(strict_types=1);

test('visual snapshot of mutation tests when a mutant escaped', function (): void {
    expect(mutationTestOutput('Untested'))
        ->toMatchSnapshot();
});
