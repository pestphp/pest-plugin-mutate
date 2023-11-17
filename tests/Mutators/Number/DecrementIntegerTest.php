<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Number\DecrementInteger;

it('decrements all integers by one', function (): void {
    expect(mutateCode(DecrementInteger::class, <<<'CODE'
        <?php

        $a = 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 0;
        CODE);
});

it('decrements negative integers by one', function (): void {
    expect(mutateCode(DecrementInteger::class, <<<'CODE'
        <?php

        $a = -1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = -2;
        CODE);
});

it('does not mutate declare strict types 1', function (): void {
    mutateCode(DecrementInteger::class, <<<'CODE'
        <?php

        declare (strict_types=1);
        CODE);
})->expectExceptionMessage('No mutation performed');

it('does not mutate int min', function (): void {
    mutateCode(DecrementInteger::class, <<<'CODE'
        <?php

        $a = -9223372036854775807;
        CODE);
})->expectExceptionMessage('No mutation performed');
