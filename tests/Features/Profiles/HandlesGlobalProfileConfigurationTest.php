<?php

declare(strict_types=1);
use Pest\Mutate\Mutators;
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Sets\ArithmeticSet;
use Pest\Mutate\Mutators\Sets\DefaultSet;
use Pest\Mutate\Profiles;

beforeEach(function (): void {
    $this->profile = Profiles::get('default');
});

test('configure default profile globally', function (): void {
    mutate()
        ->min(10.0);

    expect($this->profile->minMSI)
        ->toEqual(10.0);
});

test('configure non default profile globally', function (): void {
    mutate('profile-1')
        ->min(20.0);

    $this->profile = Profiles::get('profile-1');

    expect($this->profile->minMSI)
        ->toEqual(20.0);
});

test('globally configure paths', function (): void {
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

test('globally configure mutators', function (): void {
    expect($this->profile->mutators)
        ->toEqual(DefaultSet::mutators());

    mutate()
        ->mutators(Mutators::SET_ARITHMETIC);

    expect($this->profile->mutators)
        ->toEqual(ArithmeticSet::mutators());

    mutate()
        ->mutators(Mutators::ARITHMETIC_PLUS_TO_MINUS);

    expect($this->profile->mutators)
        ->toEqual([PlusToMinus::class]);

    mutate()
        ->mutators(Mutators::ARITHMETIC_PLUS_TO_MINUS, Mutators::ARITHMETIC_MINUS_TO_PLUS);

    expect($this->profile->mutators)
        ->toEqual([PlusToMinus::class, MinusToPlus::class]);
});

test('globally configure min MSI threshold', function (): void {
    expect($this->profile->minMSI)
        ->toEqual(0);

    mutate()
        ->min(10.0);

    expect($this->profile->minMSI)
        ->toEqual(10.0);
});

test('globally configure covered only option', function (): void {
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

test('globally configure parallel option', function (): void {
    expect($this->profile->parallel)
        ->toBeFalse();

    mutate()
        ->parallel();

    expect($this->profile->parallel)
        ->toBeTrue();

    mutate()
        ->parallel(false);

    expect($this->profile->parallel)
        ->toBeFalse();
});
