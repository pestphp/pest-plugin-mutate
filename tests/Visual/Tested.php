<?php

declare(strict_types=1);

test('visual snapshot of mutation tests on success', function (): void {
    expect(mutationTestOutput('Tested'))
        ->toMatchSnapshot();
});
