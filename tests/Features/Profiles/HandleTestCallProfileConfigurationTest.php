<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Equality\GreaterToGreaterOrEqual;
use Pest\Mutate\Profile;
use Pest\Mutate\Profiles;

beforeEach(function (): void {
    $this->profile = Profiles::get(Profile::FAKE);
});

it('forwards calls to the original test call', function (): never {
    throw new Exception('test exception');
})->mutate(Profile::FAKE)
    ->throws('test exception');

it('sets the min MSI from test', function (): void {
    expect($this->profile->minMSI)
        ->toEqual(2.0);
})->mutate(Profile::FAKE)
    ->min(2);

it('sets the covered only from test', function (): void {
    expect($this->profile->coveredOnly)
        ->toBeTrue();
})->mutate(Profile::FAKE)
    ->coveredOnly(true);

it('sets the paths from test', function (): void {
    expect($this->profile->paths)
        ->toBe(['src/folder-1', 'src/folder-2']);
})->mutate(Profile::FAKE)
    ->paths('src/folder-1', 'src/folder-2');

it('sets the mutators from test', function (): void {
    expect($this->profile->mutators)
        ->toBe([PlusToMinus::class, GreaterToGreaterOrEqual::class]);
})->mutate(Profile::FAKE)
    ->mutators(PlusToMinus::class, GreaterToGreaterOrEqual::class);

it('sets the parallel option from test', function (): void {
    expect($this->profile->parallel)
        ->toBeTrue();
})->mutate(Profile::FAKE)
    ->parallel(true);
