<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Equality\GreaterToGreaterOrEqual;
use Pest\Mutate\Profile;
use Pest\Mutate\Profiles;
use Tests\Fixtures\Classes\AgeHelper;

it('forwards calls to the original test call', function (): never {
    throw new Exception('test exception');
})->mutate(Profile::FAKE)
    ->throws('test exception');

it('sets the min MSI from test', function (): void {
    $profile = Profiles::get(Profile::FAKE.'_1');

    expect($profile->minMSI)
        ->toEqual(2.0);
})->mutate(Profile::FAKE.'_1')
    ->min(2);

it('sets the covered only from test', function (): void {
    $profile = Profiles::get(Profile::FAKE.'_2');

    expect($profile->coveredOnly)
        ->toBeTrue();
})->mutate(Profile::FAKE.'_2')
    ->coveredOnly(true);

it('sets the paths from test', function (): void {
    $profile = Profiles::get(Profile::FAKE.'_3');

    expect($profile->paths)
        ->toBe(['src/folder-1', 'src/folder-2']);
})->mutate(Profile::FAKE.'_3')
    ->path('src/folder-1', 'src/folder-2');

it('sets the mutators from test', function (): void {
    $profile = Profiles::get(Profile::FAKE.'_4');

    expect($profile->mutators)
        ->toBe([PlusToMinus::class, GreaterToGreaterOrEqual::class]);
})->mutate(Profile::FAKE.'_4')
    ->mutator(PlusToMinus::class, GreaterToGreaterOrEqual::class);

it('sets the parallel option from test', function (): void {
    $profile = Profiles::get(Profile::FAKE.'_5');

    expect($profile->parallel)
        ->toBeTrue();
})->mutate(Profile::FAKE.'_5')
    ->parallel(true);

it('sets the class option from test', function (): void {
    $profile = Profiles::get(Profile::FAKE.'_6');

    expect($profile->classes)
        ->toBe([AgeHelper::class]);
})->mutate(Profile::FAKE.'_6')
    ->class(AgeHelper::class);
