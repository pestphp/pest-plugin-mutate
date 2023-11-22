<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\String\NotEmptyStringToEmpty;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;

it('mutates a not empty string to an empty string', function (): void {
    expect(mutateCode(NotEmptyStringToEmpty::class, <<<'CODE'
        <?php

        $a = 'foo';
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = '';
        CODE);
});

it('mutates only array values but not the keys', function (): void {
    expect(mutateCode(NotEmptyStringToEmpty::class, <<<'CODE'
        <?php

        return ['foo' => 'bar'];
        CODE))->toBe(<<<'CODE'
        <?php
        
        return ['foo' => ''];
        CODE);
});

it('can not mutate an empty string', function (): void {
    expect(NotEmptyStringToEmpty::can(new String_('')))
        ->toBeFalse();
});

it('can not mutate non string nodes', function (): void {
    expect(NotEmptyStringToEmpty::can(new LNumber(1)))
        ->toBeFalse();
});

it('can not mutate strings used as array keys', function (): void {
    mutateCode(NotEmptyStringToEmpty::class, <<<'CODE'
        <?php

        $a = $b['asdfc'];
        CODE);
})->expectExceptionMessage('No mutation performed');
