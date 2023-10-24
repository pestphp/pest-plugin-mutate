<?php

declare(strict_types=1);

test('visual snapshot of mutation tests on success', function () {
    $testsPath = dirname(__DIR__);

    $process = (new Symfony\Component\Process\Process(
        ['php', 'vendor/bin/pest', '--mutate', '--covered-only', '--group=success'],
        dirname($testsPath),
    ));

    $process->run();

    $output = $process->getOutput();

    expect($output)
        ->toMatchSnapshot();
});
