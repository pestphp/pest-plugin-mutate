<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\String\EmptyStringToNotEmpty;
use PhpParser\Node\Stmt\InlineHTML;

it('mutates an empty string to a not empty string', function (): void {
    expect(mutateCode(EmptyStringToNotEmpty::class, <<<'CODE'
        <?php

        $a = '';
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'PEST Mutator was here!';
        CODE);
});

it('does not mutate a not empty string', function (): void {
    mutateCode(EmptyStringToNotEmpty::class, <<<'CODE'
        <?php

        $a = 'foo';
        CODE);
})->expectExceptionMessage('No mutation performed');

it('does not mutate a not string element', function (): void {
    mutateCode(EmptyStringToNotEmpty::class, <<<'CODE'
        <?php

        $a = 1;
        CODE);
})->expectExceptionMessage('No mutation performed');

it('can not mutate non string nodes', function (): void {
    expect(EmptyStringToNotEmpty::can(new InlineHTML('')))
        ->toBeFalse();
});
