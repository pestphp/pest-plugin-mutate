<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Laravel\Unwrap\LaravelUnwrapStrUpper;

it('unwraps the Str::upper function', function (): void {
    expect(mutateCode(LaravelUnwrapStrUpper::class, <<<'CODE'
        <?php
        
        $a = Illuminate\Support\Str::upper('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});

it('unwraps the Str::upper function when imported', function (): void {
    expect(mutateCode(LaravelUnwrapStrUpper::class, <<<'CODE'
        <?php

        use Illuminate\Support\Str;

        $a = Str::upper('foo');
        CODE))->toBe(<<<'CODE'
        <?php

        use Illuminate\Support\Str;
        $a = 'foo';
        CODE);
});

it('unwraps the Str::upper function when imported partially', function (): void {
    expect(mutateCode(LaravelUnwrapStrUpper::class, <<<'CODE'
        <?php

        use Illuminate\Support;

        $a = Support\Str::upper('foo');
        CODE))->toBe(<<<'CODE'
        <?php

        use Illuminate\Support;
        $a = 'foo';
        CODE);
});

it('unwraps the upper function when called via helper function', function (): void {
    expect(mutateCode(LaravelUnwrapStrUpper::class, <<<'CODE'
        <?php

        $a = str()->upper('foo');
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 'foo';
        CODE);
});
