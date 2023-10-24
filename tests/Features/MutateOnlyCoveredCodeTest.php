<?php

declare(strict_types=1);

use Pest\Mutate\Plugins\Mutate;
use Symfony\Component\Console\Output\ConsoleOutput;

it('enables covered only option if --covered-only argument is passed', function () {
    $plugin = new Mutate(new ConsoleOutput());

    expect($plugin->config->coveredOnly)->toBeFalse();

    $plugin->handleArguments([]);
    expect($plugin->config->coveredOnly)->toBeFalse();

    $plugin->handleArguments(['--covered-only']);
    expect($plugin->config->coveredOnly)->toBeTrue();

    $plugin->handleArguments(['--covered-only=true']);
    expect($plugin->config->coveredOnly)->toBeTrue();

    $plugin->handleArguments(['--covered-only=false']);
    expect($plugin->config->coveredOnly)->toBeFalse();
});
