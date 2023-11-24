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

it('sets the paths if --path argument is passed', function (): void {
    $this->configuration->fromArguments(['--path=app']);

    expect($this->configuration->toArray())
        ->paths->toEqual(['app']);

    $this->configuration->fromArguments(['--path=src/path-1,src/path-2']);
    expect($this->configuration->toArray())
        ->paths->toEqual(['src/path-1', 'src/path-2']);
});

it('sets the paths to ignore if --ignore argument is passed', function (): void {
    $this->configuration->fromArguments(['--ignore=src/path-1']);

    expect($this->configuration->toArray())
        ->paths_to_ignore->toEqual(['src/path-1']);

    $this->configuration->fromArguments(['--ignore=src/path-1,src/path-2']);
    expect($this->configuration->toArray())
        ->paths_to_ignore->toEqual(['src/path-1', 'src/path-2']);
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

it('excludes mutators if --except argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutator=SetArithmetic', '--except=ArithmeticPlusToMinus']);
    expect($this->configuration->toArray())
        ->mutators->toHaveCount(count(ArithmeticSet::mutators()) - 1);

    $this->configuration->fromArguments(['--mutator=SetArithmetic', '--except=ArithmeticPlusToMinus,ArithmeticMinusToPlus']);
    expect($this->configuration->toArray())
        ->mutators->toHaveCount(count(ArithmeticSet::mutators()) - 2);
});

it('sets min score threshold if --min argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->min_score->toEqual(0.0);

    $this->configuration->fromArguments(['--min=2']);
    expect($this->configuration->toArray())
        ->min_score->toEqual(2.0);

    $this->configuration->fromArguments(['--min=2.4']);
    expect($this->configuration->toArray())
        ->min_score->toEqual(2.4);
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

it('sets the processes option if --processes argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->processes->toBeNull();

    $this->configuration->fromArguments(['--processes=10']);
    expect($this->configuration->toArray())
        ->processes->toBe(10);

    $this->configuration->fromArguments(['--processes']);
    expect($this->configuration->toArray())
        ->processes->toBeNull();
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

it('enables uncommitted only option if --uncommitted-only argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->uncommitted_only->toBeNull();

    $this->configuration->fromArguments(['--uncommitted-only']);
    expect($this->configuration->toArray())
        ->uncommitted_only->toBeTrue();
});

it('enables changed only option if --changed-only argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->changed_only->toBeNull();

    $this->configuration->fromArguments(['--changed-only']);
    expect($this->configuration->toArray())
        ->changed_only->toBe('main');

    $this->configuration->fromArguments(['--changed-only=other-branch']);
    expect($this->configuration->toArray())
        ->changed_only->toBe('other-branch');
});
