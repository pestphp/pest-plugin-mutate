<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Equality\GreaterToGreaterOrEqual;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Support\Container;
use Tests\Fixtures\Classes\AgeHelper;

beforeEach(function (): void {
    $this->repository = Container::getInstance()->get(ConfigurationRepository::class);
});

it('forwards calls to the original test call', function (): never {
    throw new Exception('test exception');
})->mutate(ConfigurationRepository::FAKE)
    ->throws('test exception');

it('sets the min MSI from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_1');

    expect($configuration->toArray()['min_msi'])
        ->toEqual(2.0);
})->mutate(ConfigurationRepository::FAKE.'_1')
    ->min(2);

it('sets the covered only from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_2');

    expect($configuration->toArray()['covered_only'])
        ->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_2')
    ->coveredOnly(true);

it('sets the paths from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_3');

    expect($configuration->toArray()['paths'])
        ->toBe(['src/folder-1', 'src/folder-2']);
})->mutate(ConfigurationRepository::FAKE.'_3')
    ->path('src/folder-1', 'src/folder-2');

it('sets the mutators from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_4');

    expect($configuration->toArray()['mutators'])
        ->toBe([PlusToMinus::class, GreaterToGreaterOrEqual::class]);
})->mutate(ConfigurationRepository::FAKE.'_4')
    ->mutator(PlusToMinus::class, GreaterToGreaterOrEqual::class);

it('sets the parallel option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_5');

    expect($configuration->toArray()['parallel'])
        ->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_5')
    ->parallel(true);

it('sets the class option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_6');

    expect($configuration->toArray()['classes'])
        ->toBe([AgeHelper::class]);
})->mutate(ConfigurationRepository::FAKE.'_6')
    ->class(AgeHelper::class);

it('sets the stop on survival option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_7');

    expect($configuration->toArray()['stop_on_survival'])
        ->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_7')
    ->stopOnSurvival();

it('sets the stop on uncovered option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_8');

    expect($configuration->toArray()['stop_on_uncovered'])
        ->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_8')
    ->stopOnUncovered();

it('sets the stop on survival and stop on uncovered option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_9');

    expect($configuration->toArray())
        ->stop_on_survival->toBeTrue()
        ->stop_on_uncovered->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_9')
    ->bail();
