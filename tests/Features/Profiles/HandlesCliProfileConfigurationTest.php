<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;
use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;
use Pest\Mutate\Mutators\Sets\ArithmeticSet;
use Pest\Mutate\Mutators\Sets\DefaultSet;
use Pest\Mutate\Plugins\Mutate;
use Pest\Mutate\Profile;
use Pest\Mutate\Profiles;
use Pest\Support\Container;
use Tests\Fixtures\Classes\AgeHelper;
use Tests\Fixtures\Classes\SizeHelper;

beforeEach(function (): void {
    $this->plugin = Container::getInstance()->get(Mutate::class);

    $this->profile = Profiles::get(Profile::FAKE);
});

it('overrides global values', function (): void {
    mutate(Profile::FAKE)
        ->min(10.0);

    $this->plugin->handleArguments(['--mutate=fake-profile', '--min=2']);

    expect($this->profile->minMSI)->toEqual(2.0);
});

it('sets the paths if --paths argument is passed', function (): void {
    expect($this->profile->paths)->toEqual([]);

    $this->plugin->handleArguments(['--mutate=fake-profile', '--paths=src']);
    expect($this->profile->paths)->toEqual(['src']);

    $this->plugin->handleArguments(['--mutate=fake-profile', '--paths=src/path-1,src/path-2']);
    expect($this->profile->paths)->toEqual(['src/path-1', 'src/path-2']);
});

it('sets the mutators if --mutators argument is passed', function (): void {
    expect($this->profile->mutators)->toEqual(DefaultSet::mutators());

    $this->plugin->handleArguments(['--mutate=fake-profile', '--mutators=SetArithmetic']);
    expect($this->profile->mutators)->toEqual(ArithmeticSet::mutators());

    $this->plugin->handleArguments(['--mutate=fake-profile', '--mutators=ArithmeticPlusToMinus']);
    expect($this->profile->mutators)->toEqual([PlusToMinus::class]);

    $this->plugin->handleArguments(['--mutate=fake-profile', '--mutators=ArithmeticPlusToMinus,ArithmeticMinusToPlus']);
    expect($this->profile->mutators)->toEqual([PlusToMinus::class, MinusToPlus::class]);
});

it('sets MSI threshold if --min argument is passed', function (): void {
    expect($this->profile->minMSI)->toEqual(0.0);

    $this->plugin->handleArguments(['--mutate=fake-profile']);
    expect($this->profile->minMSI)->toEqual(0.0);

    $this->plugin->handleArguments(['--mutate=fake-profile', '--min=2']);
    expect($this->profile->minMSI)->toEqual(2.0);

    $this->plugin->handleArguments(['--mutate=fake-profile', '--min=2.4']);
    expect($this->profile->minMSI)->toEqual(2.4);
});

it('enables covered only option if --covered-only argument is passed', function (): void {
    expect($this->profile->coveredOnly)->toBeFalse();

    $this->plugin->handleArguments(['--mutate=fake-profile']);
    expect($this->profile->coveredOnly)->toBeFalse();

    $this->plugin->handleArguments(['--mutate=fake-profile', '--covered-only']);
    expect($this->profile->coveredOnly)->toBeTrue();

    $this->plugin->handleArguments(['--mutate=fake-profile', '--covered-only=true']);
    expect($this->profile->coveredOnly)->toBeTrue();

    $this->plugin->handleArguments(['--mutate=fake-profile', '--covered-only=false']);
    expect($this->profile->coveredOnly)->toBeFalse();
});

it('enables parallel option if --parallel argument is passed', function (): void {
    expect($this->profile->parallel)->toBeFalse();

    $this->plugin->handleArguments(['--mutate=fake-profile']);
    expect($this->profile->parallel)->toBeFalse();

    $this->plugin->handleArguments(['--mutate=fake-profile', '--parallel']);
    expect($this->profile->parallel)->toBeTrue();

    $this->plugin->handleArguments(['--mutate=fake-profile', '--parallel=true']);
    expect($this->profile->parallel)->toBeTrue();

    $this->plugin->handleArguments(['--mutate=fake-profile', '--parallel=false']);
    expect($this->profile->coveredOnly)->toBeFalse();
});

it('sets the class if --class argument is passed', function (): void {
    expect($this->profile->classes)->toEqual([]);

    $this->plugin->handleArguments(['--mutate=fake-profile', '--class='.AgeHelper::class]);
    expect($this->profile->classes)->toEqual([AgeHelper::class]);

    $this->plugin->handleArguments(['--mutate=fake-profile', '--class='.AgeHelper::class.','.SizeHelper::class]);
    expect($this->profile->classes)->toEqual([AgeHelper::class, SizeHelper::class]);
});
