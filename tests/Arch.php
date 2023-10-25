<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

test('contract')
    ->expect('Pest\Mutate\Contracts')
    ->toBeInterface();

test('mutators')
    ->expect('Pest\Mutate\Mutators')
    ->classes()
    ->toImplement(\Pest\Mutate\Contracts\Mutator::class)
    ->ignoring('Pest\Mutate\Mutators\Sets');

test('mutator sets')
    ->expect('Pest\Mutate\Mutators\Sets')
    ->toImplement(\Pest\Mutate\Contracts\MutatorSet::class);

test('all mutators and sets have a corresponding constant', function () {
    $constants = (new ReflectionClass(\Pest\Mutate\Mutators::class))->getConstants();

    $files = Finder::create()
        ->in(__DIR__.'/../src/Mutators')
        ->name('*.php')
        ->notPath(['Concerns'])
        ->files();

    foreach ($files as $file) {
        $class = 'Pest\Mutate\Mutators\\'.$file->getRelativePath().'\\'.$file->getFilenameWithoutExtension();

        expect($class)
            ->toBeIn($constants, 'Missing constant for '.$class);
    }
});
