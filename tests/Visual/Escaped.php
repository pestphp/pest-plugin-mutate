<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;

test('visual snapshot of mutation tests when a mutant escaped', function (): void {
    $testsPath = dirname(__DIR__);

    $process = (new Process(
        ['php', 'vendor/bin/pest', '--mutate', '--group=escaped'],
        dirname($testsPath),
    ));

    $process->run();

    $output = $process->getOutput();

    expect($output)
        ->toMatchSnapshot();
})->todo();
