<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapTrim;
use PhpParser\Node\ArgPlaceholder;

it('unwraps the trim function', function (): void {
    expect(mutateCode(UnwrapTrim::class, <<<'CODE'
        <?php

        $a = trim('foo ');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo ';
        CODE);
});

it('replaces a trim used as a first class callable with an empty callable', function (): void {
    expect(mutateCode(UnwrapTrim::class, <<<'CODE'
        <?php
        
        array_map(trim(...), $array);
        CODE))->toBe(<<<'CODE'
        <?php
        
        array_map(fn($value) => $value, $array);
        CODE);
});

it('unwraps trim when a later argument is a placeholder', function (): void {
    expect(mutateCode(UnwrapTrim::class, <<<'CODE'
        <?php

        $a = trim('foo ', ?);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 'foo ';
        CODE);
})->skip(! class_exists(ArgPlaceholder::class), 'Requires PHP-Parser with partial function application support.');
