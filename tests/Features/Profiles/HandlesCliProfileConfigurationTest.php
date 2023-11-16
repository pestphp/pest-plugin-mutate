<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Sets\ArithmeticSet;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Support\Configuration\CliConfiguration;
use Tests\Fixtures\Classes\AgeHelper;
use Tests\Fixtures\Classes\SizeHelper;

beforeEach(function (): void {
    $this->configuration = new CliConfiguration();
});

it('sets the paths if --paths argument is passed', function (): void {
    $this->configuration->fromArguments(['--path=app']);

    expect($this->configuration->toArray())
        ->paths->toEqual(['app']);

    $this->configuration->fromArguments(['--path=src/path-1,src/path-2']);
    expect($this->configuration->toArray())
        ->paths->toEqual(['src/path-1', 'src/path-2']);
});

it('sets the mutators if --mutators argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutator=SetArithmetic']);
    expect($this->configuration->toArray())
        ->mutators->toEqual(ArithmeticSet::mutators());

    $this->configuration->fromArguments(['--mutator=ArithmeticPlusToMinus']);
    expect($this->configuration->toArray())
        ->mutators->toEqual([PlusToMinus::class]);

    $this->configuration->fromArguments(['--mutator=ArithmeticPlusToMinus,ArithmeticMinusToPlus']);
    expect($this->configuration->toArray())
        ->mutators->toEqual([PlusToMinus::class, MinusToPlus::class]);
});

it('sets MSI threshold if --min argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->min_msi->toEqual(0.0);

    $this->configuration->fromArguments(['--min=2']);
    expect($this->configuration->toArray())
        ->min_msi->toEqual(2.0);

    $this->configuration->fromArguments(['--min=2.4']);
    expect($this->configuration->toArray())
        ->min_msi->toEqual(2.4);
});

it('enables covered only option if --covered-only argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->covered_only->toBeNull();

    $this->configuration->fromArguments(['--covered-only']);
    expect($this->configuration->toArray())
        ->covered_only->toBeTrue();

    $this->configuration->fromArguments(['--covered-only=true']);
    expect($this->configuration->toArray())
        ->covered_only->toBeTrue();

    $this->configuration->fromArguments(['--covered-only=false']);
    expect($this->configuration->toArray())
        ->covered_only->toBeFalse();
});

it('enables parallel option if --parallel argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->parallel->toBeNull();

    $this->configuration->fromArguments(['--parallel']);
    expect($this->configuration->toArray())
        ->parallel->toBeTrue();
});

it('sets the class if --class argument is passed', function (): void {
    $this->configuration->fromArguments(['--class='.AgeHelper::class]);
    expect($this->configuration->toArray()['classes'])
        ->toEqual([AgeHelper::class]);

    $this->configuration->fromArguments(['--class='.AgeHelper::class.','.SizeHelper::class]);
    expect($this->configuration->toArray()['classes'])
        ->toEqual([AgeHelper::class, SizeHelper::class]);
});

it('enables stop on survived option if --stop-on-survived argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->stop_on_survived->toBeNull();

    $this->configuration->fromArguments(['--stop-on-survived']);
    expect($this->configuration->toArray())
        ->stop_on_survived->toBeTrue();
});

it('enables stop on not covered option if --stop-on-uncovered argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->stop_on_not_covered->toBeNull();

    $this->configuration->fromArguments(['--stop-on-not-covered']);
    expect($this->configuration->toArray())
        ->stop_on_not_covered->toBeTrue();
});

it('enables stop on survived and stop on not covered option if --bail argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->stop_on_survived->toBeNull()
        ->stop_on_not_covered->toBeNull();

    $this->configuration->fromArguments(['--bail']);
    expect($this->configuration->toArray())
        ->stop_on_survived->toBeTrue()
        ->stop_on_not_covered->toBeTrue();
});
