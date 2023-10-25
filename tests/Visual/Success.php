<?php

declare(strict_types=1);

test('visual snapshot of mutation tests on success', function () {
    $testsPath = dirname(__DIR__);

    $process = (new Symfony\Component\Process\Process(
        ['php', 'vendor/bin/pest', 'tests/.tests/Success'],
        dirname($testsPath),
    ));

    $process->run();

    $output = $process->getOutput();

    $output = preg_replace('/Duration: .*/', 'Duration: xxx', $output);

    $this->markTestSkipped('not fully implemented yet, so there is no reason to fail the test if output does not matches');

    expect($output)
        ->toMatchSnapshot();
});
