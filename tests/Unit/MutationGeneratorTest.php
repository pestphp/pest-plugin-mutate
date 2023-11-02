<?php

declare(strict_types=1);
use Pest\Mutate\Mutation;
use Pest\Mutate\Mutators\Equality\GreaterOrEqualToGreater;
use Pest\Mutate\Mutators\Equality\SmallerToSmallerOrEqual;
use Pest\Mutate\Support\MutationGenerator;
use Symfony\Component\Finder\SplFileInfo;
use Tests\Fixtures\Classes\AgeHelper;
use Tests\Fixtures\Classes\SizeHelper;

beforeEach(function (): void {
    $this->generator = new MutationGenerator();
});

it('generates mutations for the given file', function (): void {
    $mutations = $this->generator->generate(
        $file = new SplFileInfo(dirname(__DIR__).'/Fixtures/Classes/AgeHelper.php', '', ''),
        [GreaterOrEqualToGreater::class, SmallerToSmallerOrEqual::class],
        []
    );

    expect($mutations)
        ->toBeArray()
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Mutation::class)
        ->and($mutations[0])
        ->file->toBe($file)
        ->mutator->toBe(GreaterOrEqualToGreater::class)
        ->originalNode->getStartLine()->toBe(11)
        ->modifiedSource()->toMatchSnapshot()
        ->and($mutations[1])
        ->file->toBe($file)
        ->mutator->toBe(GreaterOrEqualToGreater::class)
        ->originalNode->getStartLine()->toBe(16)
        ->modifiedSource()->toMatchSnapshot()
        ->and($mutations[2])
        ->file->toBe($file)
        ->mutator->toBe(SmallerToSmallerOrEqual::class)
        ->originalNode->getStartLine()->toBe(21)
        ->modifiedSource()->toMatchSnapshot();
});

it('ignores lines with no line coverage', function (): void {
    $mutations = $this->generator->generate(
        new SplFileInfo(dirname(__DIR__).'/Fixtures/Classes/AgeHelper.php', '', ''),
        [GreaterOrEqualToGreater::class],
        [1, 2]
    );

    expect($mutations)
        ->toBeArray()
        ->toHaveCount(0);
});

it('generates mutations for the given file if it has line coverage', function (): void {
    $mutations = $this->generator->generate(
        new SplFileInfo(dirname(__DIR__).'/Fixtures/Classes/AgeHelper.php', '', ''),
        [GreaterOrEqualToGreater::class],
        [10, 11]
    );

    expect($mutations)
        ->toBeArray()
        ->toHaveCount(1);
});

it('generates mutations for the given file if it contains the given class', function (array $classes, int $expectedCount): void {
    $mutations = $this->generator->generate(
        file: new SplFileInfo(dirname(__DIR__).'/Fixtures/Classes/AgeHelper.php', '', ''),
        mutators: [GreaterOrEqualToGreater::class],
        classesToMutate: $classes,
    );

    expect($mutations)
        ->toBeArray()
        ->toHaveCount($expectedCount);
})->with([
    [[AgeHelper::class], 2],
    [[SizeHelper::class], 0],
    [[AgeHelper::class, SizeHelper::class], 2],
    [['AgeHelper'], 2],
    [['SizeHelper'], 0],
    [['Invalid\\Namespace\\AgeHelper'], 0],
    [['Invalid\\Namespace\\AgeHelper', AgeHelper::class], 2],
    [[SizeHelper::class, AgeHelper::class], 2],
]);

it('ignores lines with the ignore annotation', function (): void {
    $mutations = $this->generator->generate(
        new SplFileInfo(dirname(__DIR__).'/Fixtures/Classes/SizeHelper.php', '', ''),
        [GreaterOrEqualToGreater::class],
    );

    expect($mutations)
        ->toBeArray()
        ->toHaveCount(0);
});
