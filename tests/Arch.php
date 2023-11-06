<?php

declare(strict_types=1);
use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Contracts\Subscriber;
use Pest\Mutate\Mutators;
use PHPUnit\Event\Subscriber as PhpUnitSubscriber;
use Symfony\Component\Finder\Finder;

test('contract')
    ->expect('Pest\Mutate\Contracts')
    ->toBeInterface();

test('phpunit subscribers')
    ->expect('Pest\Mutate\Subscribers')
    ->toImplement(PhpUnitSubscriber::class)
    ->ignoring('Pest\Mutate\Subscribers\MutationTest');

test('subscribers')
    ->expect('Pest\Mutate\Subscribers\MutationTest')
    ->toImplement(Subscriber::class);

test('mutators')
    ->expect(Mutators::class)
    ->classes()
    ->toImplement(Mutator::class)
    ->ignoring('Pest\Mutate\Mutators\Sets');

test('mutator sets')
    ->expect('Pest\Mutate\Mutators\Sets')
    ->toImplement(MutatorSet::class);

test('all mutators and sets have a corresponding constant', function (): void {
    $constants = (new ReflectionClass(Mutators::class))->getConstants();

    $files = Finder::create()
        ->in(__DIR__.'/../src/Mutators')
        ->name('*.php')
        ->notPath(['Concerns', 'Abstract'])
        ->files();

    foreach ($files as $file) {
        $class = 'Pest\Mutate\Mutators\\'.str_replace('/', '\\', $file->getRelativePath()).'\\'.$file->getFilenameWithoutExtension();

        expect($class)
            ->toBeIn($constants, 'Missing constant for '.$class);
    }
});
