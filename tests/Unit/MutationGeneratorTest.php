<?php

declare(strict_types=1);
use Pest\Mutate\Mutation;
use Pest\Mutate\Mutators\ControlStructures\IfNegated;
use Pest\Mutate\Mutators\Equality\GreaterOrEqualToGreater;
use Pest\Mutate\Mutators\Equality\SmallerToSmallerOrEqual;
use Pest\Mutate\Support\MutationGenerator;
use Pest\Mutate\Support\PhpParserFactory;
use Symfony\Component\Finder\SplFileInfo;
use Tests\Fixtures\Classes\AgeHelper;
use Tests\Fixtures\Classes\SizeHelper;

beforeEach(function (): void {
    $this->generator = new MutationGenerator();

    $this->generate = function (string $content, array $mutators) {
        $fileName = tempnam(sys_get_temp_dir(), 'pest-mutate');

        file_put_contents($fileName, $content);

        return $this->generator->generate(
            new SplFileInfo($fileName, '', ''),
            $mutators,
        );
    };
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
        ->file->getRealPath()->toBe($file->getRealPath())
        ->mutator->toBe(GreaterOrEqualToGreater::class)
        ->startLine->toBe(11)
        ->modifiedSource()->toMatchSnapshot()
        ->and($mutations[1])
        ->file->getRealPath()->toBe($file->getRealPath())
        ->mutator->toBe(GreaterOrEqualToGreater::class)
        ->startLine->toBe(16)
        ->modifiedSource()->toMatchSnapshot()
        ->and($mutations[2])
        ->file->getRealPath()->toBe($file->getRealPath())
        ->mutator->toBe(SmallerToSmallerOrEqual::class)
        ->startLine->toBe(21)
        ->modifiedSource()->toMatchSnapshot();
})->skip(PhpParserFactory::version() === 4);

it('generates mutations for the given file with v4 parser', function (): void {
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
        ->file->getRealPath()->toBe($file->getRealPath())
        ->mutator->toBe(GreaterOrEqualToGreater::class)
        ->startLine->toBe(11)
        ->modifiedSource()->toMatchSnapshot()
        ->and($mutations[1])
        ->file->getRealPath()->toBe($file->getRealPath())
        ->mutator->toBe(GreaterOrEqualToGreater::class)
        ->startLine->toBe(16)
        ->modifiedSource()->toMatchSnapshot()
        ->and($mutations[2])
        ->file->getRealPath()->toBe($file->getRealPath())
        ->mutator->toBe(SmallerToSmallerOrEqual::class)
        ->startLine->toBe(21)
        ->modifiedSource()->toMatchSnapshot();
})->skip(PhpParserFactory::version() !== 4);

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
    [[AgeHelper::class], 2],
    [['Tests\\Fixtures\\Classes\\AgeHelp'], 2],
    [['Invalid\\Namespace\\AgeHelper'], 0],
    [['Invalid\\Namespace\\AgeHelp'], 0],
    [['Invalid\\Namespace\\AgeHelper', AgeHelper::class], 2],
    [[SizeHelper::class, AgeHelper::class], 2],
]);

it('ignores lines with the ignore annotation', function (): void {
    $mutations = ($this->generate)(<<<'PHP'
            <?php

            return $a >= $b; // @pest-mutate-ignore
            PHP,
        [GreaterOrEqualToGreater::class],
    );

    expect($mutations)
        ->toBeEmpty();
});

it('ignores lines with the ignore annotation for a specific mutator', function (): void {
    $mutations = ($this->generate)(<<<'PHP'
            <?php

            if($a > $b) { // @pest-mutate-ignore: GreaterOrEqualToGreater
                $a = $b;
            }
            PHP,
        [GreaterOrEqualToGreater::class, IfNegated::class],
    );

    expect($mutations)
        ->toHaveCount(1)
        ->{0}->mutator->toBe(IfNegated::class);
});

it('ignores lines with the ignore annotation for multiple mutators', function (): void {
    $mutations = ($this->generate)(<<<'PHP'
            <?php

            if($a > $b) { // @pest-mutate-ignore: GreaterOrEqualToGreater,IfNegated
                $a = $b;
            }
            PHP,
        [GreaterOrEqualToGreater::class, IfNegated::class],
    );

    expect($mutations)
        ->toBeEmpty();
});

it('ignores all mutators for a function', function (): void {
    $mutations = ($this->generate)(<<<'PHP'
            <?php

            /**
             * @pest-mutate-ignore
             */
            function test() {
                return $a >= $b;
            }
            PHP,
        [GreaterOrEqualToGreater::class],
    );

    expect($mutations)
        ->toBeEmpty();
});

it('ignores a specific mutator for a function', function (): void {
    $mutations = ($this->generate)(<<<'PHP'
            <?php

            /**
             * @pest-mutate-ignore: GreaterOrEqualToGreater
             */
            function test() {
                if($a > $b) {
                    return $a = $b;
                }
            }
            PHP,
        [GreaterOrEqualToGreater::class, IfNegated::class],
    );

    expect($mutations)
        ->toHaveCount(1)
        ->{0}->mutator->toBe(IfNegated::class);
});

it('ignores multiple mutators for a function', function (): void {
    $mutations = ($this->generate)(<<<'PHP'
            <?php

            /**
             * @pest-mutate-ignore: GreaterOrEqualToGreater,IfNegated
             */
            function test() {
                if($a >= $b) {
                    return $a = $b;
                }
            }
            PHP,
        [GreaterOrEqualToGreater::class, IfNegated::class],
    );

    expect($mutations)
        ->toBeEmpty();
});

it('ignores multiple mutators for a function with a single line doc block', function (): void {
    $mutations = ($this->generate)(<<<'PHP'
            <?php

            /** @pest-mutate-ignore: GreaterOrEqualToGreater,IfNegated */
            function test() {
                if($a >= $b) {
                    return $a = $b;
                }
            }
            PHP,
        [GreaterOrEqualToGreater::class, IfNegated::class],
    );

    expect($mutations)
        ->toBeEmpty();
});

it('ignores multiple mutators for a class', function (): void {
    $mutations = ($this->generate)(<<<'PHP'
            <?php

            /**
             * @pest-mutate-ignore: GreaterOrEqualToGreater,IfNegated
             */
            class Test {
                public function test() {
                    if($a >= $b) {
                        return $a = $b;
                    }
                }
            }
            PHP,
        [GreaterOrEqualToGreater::class, IfNegated::class],
    );

    expect($mutations)
        ->toBeEmpty();
});

it('ignores multiple mutators for a loop', function (): void {
    $mutations = ($this->generate)(<<<'PHP'
            <?php

            function test() {
                /**
                 * @pest-mutate-ignore: GreaterOrEqualToGreater,IfNegated
                 */
                for($i = 0; $i < 10; $i++) {
                    if($a >= $b) {
                        return $a = $b;
                    }
                }
            }
            PHP,
        [GreaterOrEqualToGreater::class, IfNegated::class],
    );

    expect($mutations)
        ->toBeEmpty();
});
