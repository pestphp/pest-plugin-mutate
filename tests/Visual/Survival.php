<?php

declare(strict_types=1);

test('visual snapshot of mutation tests when a mutant survived', function () {
    $testsPath = dirname(__DIR__);

    $process = (new Symfony\Component\Process\Process(
        ['php', 'vendor/bin/pest', '--mutate', '--group=survival'],
        dirname($testsPath),
    ));

    $process->run();

    $output = $process->getOutput();

    expect($output)
        ->toMatchSnapshot();
})->todo();
