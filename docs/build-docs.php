<?php

declare(strict_types=1);

use Pest\Mutate\Support\DocGenerator;

include __DIR__.'/../vendor/autoload.php';

// load skeleton
$readMe = file_get_contents(__DIR__.'/README.stub.md');

// replace file placeholders
$readMe = str_replace('___mutation-testing.md___', file_get_contents(__DIR__.'/mutation-testing.md'), $readMe);
$readMe = str_replace('___mutator-reference.md___', file_get_contents(__DIR__.'/mutator-reference.md'), $readMe);

// replace sets placeholder
$readMe = str_replace('___SETS___', DocGenerator::buildSets(), $readMe);

// replace mutators placeholder
$readMe = str_replace('___MUTATORS___', DocGenerator::buildMutators(), $readMe);

file_put_contents(__DIR__.'/../README.md', $readMe);
