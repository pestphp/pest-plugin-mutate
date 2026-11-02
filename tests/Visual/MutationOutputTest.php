<?php

declare(strict_types=1);

// Keep these tests together: their subprocesses share Pest's coverage report.
test('visual snapshot of mutation tests on success', function (): void {
    expect(mutationTestOutput('Tested'))
        ->toMatchSnapshot();
});

test('visual snapshot of mutation tests with describe on success', function (): void {
    expect(mutationTestOutput('TestedWithDescribe'))
        ->toMatchSnapshot();
});

test('visual snapshot of mutation tests when a mutant escaped', function (): void {
    expect(mutationTestOutput('Untested'))
        ->toMatchSnapshot();
});
