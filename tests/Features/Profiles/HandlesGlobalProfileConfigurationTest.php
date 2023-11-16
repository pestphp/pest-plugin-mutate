<?php

declare(strict_types=1);
use Pest\Mutate\Mutators;
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Sets\ArithmeticSet;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Support\Container;
use Tests\Fixtures\Classes\AgeHelper;

beforeEach(function (): void {
    $this->configuration = Container::getInstance()->get(ConfigurationRepository::class)
        ->globalConfiguration(ConfigurationRepository::FAKE);
});

test('configure profile globally', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->min(20.0);

    expect($this->configuration->toArray()['min_msi'])
        ->toEqual(20.0);
});

test('globally configure paths', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->path(['app']);

    expect($this->configuration->toArray()['paths'])
        ->toEqual(['app']);

    mutate(ConfigurationRepository::FAKE)
        ->path(['src/path-1', 'src/path-2'], 'src/path-3');

    expect($this->configuration->toArray()['paths'])
        ->toEqual(['src/path-1', 'src/path-2', 'src/path-3']);
});

test('globally configure mutators', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->mutator(Mutators::SET_ARITHMETIC);

    expect($this->configuration->toArray()['mutators'])
        ->toEqual(ArithmeticSet::mutators());

    mutate(ConfigurationRepository::FAKE)
        ->mutator(Mutators::ARITHMETIC_PLUS_TO_MINUS);

    expect($this->configuration->toArray()['mutators'])
        ->toEqual([PlusToMinus::class]);

    mutate(ConfigurationRepository::FAKE)
        ->mutator(Mutators::ARITHMETIC_PLUS_TO_MINUS, Mutators::ARITHMETIC_MINUS_TO_PLUS);

    expect($this->configuration->toArray()['mutators'])
        ->toEqual([PlusToMinus::class, MinusToPlus::class]);
});

test('globally configure min MSI threshold', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->min(10.0);

    expect($this->configuration->toArray()['min_msi'])
        ->toEqual(10.0);
});

test('globally configure covered only option', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->coveredOnly();

    expect($this->configuration->toArray()['covered_only'])
        ->toBeTrue();

    mutate(ConfigurationRepository::FAKE)
        ->coveredOnly(false);

    expect($this->configuration->toArray()['covered_only'])
        ->toBeFalse();
});

test('globally configure parallel option', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->parallel();

    expect($this->configuration->toArray()['parallel'])
        ->toBeTrue();

    mutate(ConfigurationRepository::FAKE)
        ->parallel(false);

    expect($this->configuration->toArray()['parallel'])
        ->toBeFalse();
});

test('globally configure class option', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->class(AgeHelper::class);

    expect($this->configuration->toArray()['classes'])
        ->toBe([AgeHelper::class]);
});

test('globally configure stop on survived', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->stopOnSurvived();

    expect($this->configuration->toArray()['stop_on_survived'])
        ->toBeTrue();
});

test('globally configure stop on not covered', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->stopOnNotCovered();

    expect($this->configuration->toArray()['stop_on_not_covered'])
        ->toBeTrue();
});

test('globally configure bail', function (): void {
    mutate(ConfigurationRepository::FAKE)
        ->bail();

    expect($this->configuration->toArray())
        ->stop_on_survived->toBeTrue()
        ->stop_on_not_covered->toBeTrue();
});
