<?php

declare(strict_types=1);

use Pest\Mutate\Profiles;

beforeEach(function () {
    $this->profile = Profiles::get('default');
});

test('configure default profile globally', function () {
    mutate()
        ->min(10.0);

    expect($this->profile->minMSI)
        ->toEqual(10.0);
});

test('configure non default profile globally', function () {
    mutate('profile-1')
        ->min(20.0);

    $this->profile = Profiles::get('profile-1');

    expect($this->profile->minMSI)
        ->toEqual(20.0);
});

test('globally configure min MSI threshold', function () {
    expect($this->profile->minMSI)
        ->toEqual(0);

    mutate()
        ->min(10.0);

    expect($this->profile->minMSI)
        ->toEqual(10.0);
});

test('globally configure paths', function () {
    expect($this->profile->paths)
        ->toEqual([]);

    mutate()
        ->paths(['src']);

    expect($this->profile->paths)
        ->toEqual(['src']);

    mutate()
        ->paths(['src/path-1', 'src/path-2'], 'src/path-3');

    expect($this->profile->paths)
        ->toEqual(['src/path-1', 'src/path-2', 'src/path-3']);
});

test('globally configure covered only option', function () {
    expect($this->profile->coveredOnly)
        ->toBeFalse();

    mutate()
        ->coveredOnly();

    expect($this->profile->coveredOnly)
        ->toBeTrue();

    mutate()
        ->coveredOnly(false);

    expect($this->profile->coveredOnly)
        ->toBeFalse();
});
