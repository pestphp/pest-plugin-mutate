<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Laravel\Remove\LaravelRemoveStringableUpper;

it('removes the str()->upper() function', function (): void {
    expect(mutateCode(LaravelRemoveStringableUpper::class, <<<'CODE'
        <?php
        
        $a = str('foo')->upper();
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = str('foo');
        CODE);
});

it('removes the str()->upper() function when chained', function (): void {
    expect(mutateCode(LaravelRemoveStringableUpper::class, <<<'CODE'
        <?php

        $a = str('foo')->append('bar')->upper();
        CODE))->toBe(<<<'CODE'
        <?php

        $a = str('foo')->append('bar');
        CODE);
});

it('removes the str()->upper() function when chained between', function (): void {
    expect(mutateCode(LaravelRemoveStringableUpper::class, <<<'CODE'
        <?php

        $a = str('foo')->upper()->append('bar');
        CODE))->toBe(<<<'CODE'
        <?php

        $a = str('foo')->append('bar');
        CODE);
});

it('removes the Str::of()->upper() function', function (): void {
    expect(mutateCode(LaravelRemoveStringableUpper::class, <<<'CODE'
        <?php
        
        use Illuminate\Support\Str;

        $a = Str::of('foo')->upper();
        CODE))->toBe(<<<'CODE'
        <?php

        use Illuminate\Support\Str;
        $a = Str::of('foo');
        CODE);
});

it('does not mutate if the str method is called without a parameter', function (): void {
    mutateCode(LaravelRemoveStringableUpper::class, <<<'CODE'
        <?php
        
        $a = str()->upper();
        CODE);
})->expectExceptionMessage('No mutation performed');
