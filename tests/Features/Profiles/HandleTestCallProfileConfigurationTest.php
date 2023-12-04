<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Equality\GreaterToGreaterOrEqual;
use Pest\Mutate\Mutators\Sets\ArithmeticSet;
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

it('sets the min score threshold from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_1');

    expect($configuration->toArray()['min_score'])
        ->toEqual(2.0);
})->mutate(ConfigurationRepository::FAKE.'_1')
    ->min(2);

it('sets the min score threshold and ignores zero mutations from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_15');

    expect($configuration->toArray())
        ->min_score->toEqual(2.0);
})->mutate(ConfigurationRepository::FAKE.'_15')
    ->min(2, failOnZeroMutations: false);

it('sets the  from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_16');

    expect($configuration->toArray())
        ->ignore_min_score_on_zero_mutations->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_16')
    ->ignoreMinScoreOnZeroMutations();

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

it('sets the paths to ignore from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_14');

    expect($configuration->toArray()['paths_to_ignore'])
        ->toBe(['src/folder-1', 'src/folder-2']);
})->mutate(ConfigurationRepository::FAKE.'_14')
    ->ignore('src/folder-1', 'src/folder-2');

it('sets the mutators from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_4');

    expect($configuration->toArray()['mutators'])
        ->toBe([PlusToMinus::class, GreaterToGreaterOrEqual::class]);
})->mutate(ConfigurationRepository::FAKE.'_4')
    ->mutator(PlusToMinus::class, GreaterToGreaterOrEqual::class);

it('excludes some mutators from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_10');

    expect($configuration->toArray()['mutators'])
        ->toHaveCount(count(ArithmeticSet::mutators()) - 2);
})->mutate(ConfigurationRepository::FAKE.'_10')
    ->mutator(ArithmeticSet::class)
    ->except(PlusToMinus::class, MinusToPlus::class);

it('sets the parallel option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_5');

    expect($configuration->toArray()['parallel'])
        ->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_5')
    ->parallel(true);

it('sets the processes option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_13');

    expect($configuration->toArray()['processes'])
        ->toBe(10);
})->mutate(ConfigurationRepository::FAKE.'_13')
    ->processes(10);

it('sets the class option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_6');

    expect($configuration->toArray()['classes'])
        ->toBe([AgeHelper::class]);
})->mutate(ConfigurationRepository::FAKE.'_6')
    ->class(AgeHelper::class);

it('sets the stop on survived option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_7');

    expect($configuration->toArray()['stop_on_survived'])
        ->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_7')
    ->stopOnSurvived();

it('sets the stop on not covered option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_8');

    expect($configuration->toArray()['stop_on_not_covered'])
        ->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_8')
    ->stopOnNotCovered();

it('sets the stop on survived and stop on not covered option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_9');

    expect($configuration->toArray())
        ->stop_on_survived->toBeTrue()
        ->stop_on_not_covered->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_9')
    ->bail();

it('sets the uncommitted only option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_11');

    expect($configuration->toArray()['uncommitted_only'])
        ->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_11')
    ->uncommittedOnly(true);

it('sets the changed only option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_12');

    expect($configuration->toArray()['changed_only'])
        ->toBe('other-branch');
})->mutate(ConfigurationRepository::FAKE.'_12')
    ->changedOnly('other-branch');

it('sets the profile option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_17');

    expect($configuration->toArray()['profile'])
        ->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_17')
    ->profile(true);

it('sets the retry option from test', function (): void {
    $configuration = $this->repository->fakeTestConfiguration(ConfigurationRepository::FAKE.'_18');

    expect($configuration->toArray())
        ->retry->toBeTrue()
        ->stop_on_survived->toBeTrue();
})->mutate(ConfigurationRepository::FAKE.'_18')
    ->retry(true);
