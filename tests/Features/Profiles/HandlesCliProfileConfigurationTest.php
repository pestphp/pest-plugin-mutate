<?php

declare(strict_types=1);

use Pest\Mutate\Plugins\Mutate;
use Pest\Mutate\Profiles;
use Pest\Mutate\Tester\MutationTester;
use Symfony\Component\Console\Output\ConsoleOutput;

beforeEach(function () {
    MutationTester::fake();

    $this->plugin = new Mutate(new ConsoleOutput());

    $this->profile = Profiles::get('default');
});

it('overrides global values on the default profile', function () {
    mutate()
        ->min(10.0);

    $this->plugin->handleArguments(['--mutate', '--min=2']);

    expect($this->profile->minMSI)->toEqual(2.0);
});

it('overrides global values on a non default profile', function () {
    mutate('profile-1')
        ->min(10.0);

    $this->profile = Profiles::get('profile-1');

    $this->plugin->handleArguments(['--mutate=profile-1', '--min=2']);

    expect($this->profile->minMSI)->toEqual(2.0);
});

it('sets the paths if --paths argument is passed', function () {
    expect($this->profile->paths)->toEqual([]);

    $this->plugin->handleArguments(['--mutate', '--paths=src']);
    expect($this->profile->paths)->toEqual(['src']);

    $this->plugin->handleArguments(['--mutate', '--paths=src/path-1,src/path-2']);
    expect($this->profile->paths)->toEqual(['src/path-1', 'src/path-2']);
});

it('sets MSI threshold if --min argument is passed', function () {
    expect($this->profile->minMSI)->toEqual(0.0);

    $this->plugin->handleArguments(['--mutate']);
    expect($this->profile->minMSI)->toEqual(0.0);

    $this->plugin->handleArguments(['--mutate', '--min=2']);
    expect($this->profile->minMSI)->toEqual(2.0);

    $this->plugin->handleArguments(['--mutate', '--min=2.4']);
    expect($this->profile->minMSI)->toEqual(2.4);
});

it('enables covered only option if --covered-only argument is passed', function () {
    expect($this->profile->coveredOnly)->toBeFalse();

    $this->plugin->handleArguments(['--mutate']);
    expect($this->profile->coveredOnly)->toBeFalse();

    $this->plugin->handleArguments(['--mutate', '--covered-only']);
    expect($this->profile->coveredOnly)->toBeTrue();

    $this->plugin->handleArguments(['--mutate', '--covered-only=true']);
    expect($this->profile->coveredOnly)->toBeTrue();

    $this->plugin->handleArguments(['--mutate', '--covered-only=false']);
    expect($this->profile->coveredOnly)->toBeFalse();
});
