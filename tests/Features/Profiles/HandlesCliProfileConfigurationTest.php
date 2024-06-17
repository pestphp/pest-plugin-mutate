<?php

declare(strict_types=1);
use Pest\Mutate\Cache\FileStore;
use Pest\Mutate\Cache\NullStore;
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Sets\ArithmeticSet;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Support\Configuration\CliConfiguration;
use Pest\Support\Container;
use Psr\SimpleCache\CacheInterface;
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

it('enables ignore min score on zero mutations option if --ignore-min-score-on-zero-mutations argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->ignore_min_score_on_zero_mutations->toBeNull();

    $this->configuration->fromArguments(['--ignore-min-score-on-zero-mutations']);
    expect($this->configuration->toArray())
        ->ignore_min_score_on_zero_mutations->toBeTrue();

    $this->configuration->fromArguments(['--ignore-min-score-on-zero-mutations=true']);
    expect($this->configuration->toArray())
        ->ignore_min_score_on_zero_mutations->toBeTrue();

    $this->configuration->fromArguments(['--ignore-min-score-on-zero-mutations=false']);
    expect($this->configuration->toArray())
        ->ignore_min_score_on_zero_mutations->toBeFalse();
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

it('enables stop on escaped option if --stop-on-escaped argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->stop_on_escaped->toBeNull();

    $this->configuration->fromArguments(['--stop-on-escaped']);
    expect($this->configuration->toArray())
        ->stop_on_escaped->toBeTrue();
});

it('enables stop on not covered option if --stop-on-uncovered argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->stop_on_not_covered->toBeNull();

    $this->configuration->fromArguments(['--stop-on-not-covered']);
    expect($this->configuration->toArray())
        ->stop_on_not_covered->toBeTrue();
});

it('enables stop on escaped and stop on not covered option if --bail argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->stop_on_escaped->toBeNull()
        ->stop_on_not_covered->toBeNull();

    $this->configuration->fromArguments(['--bail']);
    expect($this->configuration->toArray())
        ->stop_on_escaped->toBeTrue()
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

it('enables profile option if --profile argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->profile->toBeNull();

    $this->configuration->fromArguments(['--profile']);
    expect($this->configuration->toArray())
        ->profile->toBeTrue();
});

it('enables profile option if --retry argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->retry->toBeNull()
        ->stop_on_escaped->toBeNull();

    $this->configuration->fromArguments(['--retry']);
    expect($this->configuration->toArray())
        ->retry->toBeTrue()
        ->stop_on_escaped->toBeTrue();
});

it('enables the NullStore if --no-cache argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect(Container::getInstance()->get(CacheInterface::class))
        ->toBeInstanceOf(FileStore::class);

    $this->configuration->fromArguments(['--no-cache']);
    expect(Container::getInstance()->get(CacheInterface::class))
        ->toBeInstanceOf(NullStore::class);
});

it('enables the mutatino id filter if --id argument is passed', function (): void {
    $this->configuration->fromArguments(['--mutate='.ConfigurationRepository::FAKE]);
    expect($this->configuration->toArray())
        ->mutation_id->toBeNull();

    $this->configuration->fromArguments(['--id=fa1234']);
    expect($this->configuration->toArray())
        ->mutation_id->toBe('fa1234');
});
