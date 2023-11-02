<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;

test('visual snapshot of mutation tests on success', function (): void {
    $testsPath = dirname(__DIR__);

    $process = (new Process(
        ['php', 'vendor/bin/pest', 'tests/.tests/SuccessDescribe'],
        dirname($testsPath),
    ));

    $process->run();

    $output = $process->getOutput();

    $output = preg_replace('/Duration: .*/', 'Duration: xxx', $output);

    expect($output)
        ->toMatchSnapshot();
})->todo();
