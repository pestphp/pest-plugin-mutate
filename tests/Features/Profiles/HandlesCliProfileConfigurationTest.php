<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Sets\DefaultSet;
use Pest\Mutate\Plugins\Mutate;
use Pest\Mutate\Profiles;
use Pest\Mutate\Tester\MutationTestRunner;
use Pest\Support\Container;

beforeEach(function (): void {
    MutationTestRunner::fake();

    $this->plugin = Container::getInstance()->get(Mutate::class);

    $this->profile = Profiles::get('default');
});

it('overrides global values on the default profile', function (): void {
    mutate()
        ->min(10.0);

    $this->plugin->handleArguments(['--mutate', '--min=2']);

    expect($this->profile->minMSI)->toEqual(2.0);
});

it('overrides global values on a non default profile', function (): void {
    mutate('profile-1')
        ->min(10.0);

    $this->profile = Profiles::get('profile-1');

    $this->plugin->handleArguments(['--mutate=profile-1', '--min=2']);

    expect($this->profile->minMSI)->toEqual(2.0);
});

it('sets the paths if --paths argument is passed', function (): void {
    expect($this->profile->paths)->toEqual([]);

    $this->plugin->handleArguments(['--mutate', '--paths=src']);
    expect($this->profile->paths)->toEqual(['src']);

    $this->plugin->handleArguments(['--mutate', '--paths=src/path-1,src/path-2']);
    expect($this->profile->paths)->toEqual(['src/path-1', 'src/path-2']);
});

it('sets the mutators if --mutators argument is passed', function (): void {
    expect($this->profile->mutators)->toEqual(DefaultSet::mutators());

    $this->plugin->handleArguments(['--mutate', '--mutators=SetArithmetic']);
    expect($this->profile->mutators)->toEqual([PlusToMinus::class, MinusToPlus::class]);

    $this->plugin->handleArguments(['--mutate', '--mutators=ArithmeticPlusToMinus']);
    expect($this->profile->mutators)->toEqual([PlusToMinus::class]);

    $this->plugin->handleArguments(['--mutate', '--mutators=ArithmeticPlusToMinus,ArithmeticMinusToPlus']);
    expect($this->profile->mutators)->toEqual([PlusToMinus::class, MinusToPlus::class]);
});

it('sets MSI threshold if --min argument is passed', function (): void {
    expect($this->profile->minMSI)->toEqual(0.0);

    $this->plugin->handleArguments(['--mutate']);
    expect($this->profile->minMSI)->toEqual(0.0);

    $this->plugin->handleArguments(['--mutate', '--min=2']);
    expect($this->profile->minMSI)->toEqual(2.0);

    $this->plugin->handleArguments(['--mutate', '--min=2.4']);
    expect($this->profile->minMSI)->toEqual(2.4);
});

it('enables covered only option if --covered-only argument is passed', function (): void {
    expect($this->profile->coveredOnly)->toBeFalse();

    $this->plugin->handleArguments(['--mutate']);
    expect($this->profile->coveredOnly)->toBeFalse();

    $this->plugin->handleArguments(['--mutate', '--covered-only']);
    expect($this->profile->coveredOnly)->toBeTrue();

    $this->plugin->handleArguments(['--mutate', '--covered-only=true']);
    expect($this->profile->coveredOnly)->toBeTrue();

    $this->plugin->handleArguments(['--mutate', '--covered-only=false']);
    expect($this->profile->coveredOnly)->toBeFalse();
});

it('enables parallel option if --parallel argument is passed', function (): void {
    expect($this->profile->parallel)->toBeFalse();

    $this->plugin->handleArguments(['--mutate']);
    expect($this->profile->parallel)->toBeFalse();

    $this->plugin->handleArguments(['--mutate', '--parallel']);
    expect($this->profile->parallel)->toBeTrue();

    $this->plugin->handleArguments(['--mutate', '--parallel=true']);
    expect($this->profile->parallel)->toBeTrue();

    $this->plugin->handleArguments(['--mutate', '--parallel=false']);
    expect($this->profile->coveredOnly)->toBeFalse();
});
