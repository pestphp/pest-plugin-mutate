<?php

declare(strict_types=1);
use Pest\Mutate\Mutators;
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Sets\ArithmeticSet;
use Pest\Mutate\Mutators\Sets\DefaultSet;
use Pest\Mutate\Profile;
use Pest\Mutate\Profiles;
use Tests\Fixtures\Classes\AgeHelper;

beforeEach(function (): void {
    $this->profile = Profiles::get(Profile::FAKE);
});

test('configure profile globally', function (): void {
    mutate(Profile::FAKE)
        ->min(20.0);

    expect($this->profile->minMSI)
        ->toEqual(20.0);
});

test('globally configure paths', function (): void {
    expect($this->profile->paths)
        ->toEqual([]);

    mutate(Profile::FAKE)
        ->paths(['src']);

    expect($this->profile->paths)
        ->toEqual(['src']);

    mutate(Profile::FAKE)
        ->paths(['src/path-1', 'src/path-2'], 'src/path-3');

    expect($this->profile->paths)
        ->toEqual(['src/path-1', 'src/path-2', 'src/path-3']);
});

test('globally configure mutators', function (): void {
    expect($this->profile->mutators)
        ->toEqual(DefaultSet::mutators());

    mutate(Profile::FAKE)
        ->mutators(Mutators::SET_ARITHMETIC);

    expect($this->profile->mutators)
        ->toEqual(ArithmeticSet::mutators());

    mutate(Profile::FAKE)
        ->mutators(Mutators::ARITHMETIC_PLUS_TO_MINUS);

    expect($this->profile->mutators)
        ->toEqual([PlusToMinus::class]);

    mutate(Profile::FAKE)
        ->mutators(Mutators::ARITHMETIC_PLUS_TO_MINUS, Mutators::ARITHMETIC_MINUS_TO_PLUS);

    expect($this->profile->mutators)
        ->toEqual([PlusToMinus::class, MinusToPlus::class]);
});

test('globally configure min MSI threshold', function (): void {
    expect($this->profile->minMSI)
        ->toEqual(0);

    mutate(Profile::FAKE)
        ->min(10.0);

    expect($this->profile->minMSI)
        ->toEqual(10.0);
});

test('globally configure covered only option', function (): void {
    expect($this->profile->coveredOnly)
        ->toBeFalse();

    mutate(Profile::FAKE)
        ->coveredOnly();

    expect($this->profile->coveredOnly)
        ->toBeTrue();

    mutate(Profile::FAKE)
        ->coveredOnly(false);

    expect($this->profile->coveredOnly)
        ->toBeFalse();
});

test('globally configure parallel option', function (): void {
    expect($this->profile->parallel)
        ->toBeFalse();

    mutate(Profile::FAKE)
        ->parallel();

    expect($this->profile->parallel)
        ->toBeTrue();

    mutate(Profile::FAKE)
        ->parallel(false);

    expect($this->profile->parallel)
        ->toBeFalse();
});

test('globally configure class option', function (): void {
    expect($this->profile->classes)
        ->toBe([]);

    mutate(Profile::FAKE)
        ->class(AgeHelper::class);

    expect($this->profile->classes)
        ->toBe([AgeHelper::class]);
});
