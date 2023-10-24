<?php

declare(strict_types=1);

use Pest\Mutate\Plugin;
use Symfony\Component\Console\Output\ConsoleOutput;

it('sets MSI threshold if --min exist', function () {
    $plugin = new Plugin(new ConsoleOutput());

    expect($plugin->config->minMSI)->toEqual(0.0);

    $plugin->handleArguments([]);
    expect($plugin->config->minMSI)->toEqual(0.0);

    $plugin->handleArguments(['--min=2']);
    expect($plugin->config->minMSI)->toEqual(2.0);

    $plugin->handleArguments(['--min=2.4']);
    expect($plugin->config->minMSI)->toEqual(2.4);
});
