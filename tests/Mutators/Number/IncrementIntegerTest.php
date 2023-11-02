<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Number\IncrementInteger;

it('increments all integers by one', function (): void {
    expect(mutateCode(IncrementInteger::class, <<<'CODE'
        <?php

        $a = 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 2;
        CODE);
});

it('does not mutate declare strict types 1', function (): void {
    expect(mutateCode(IncrementInteger::class, <<<'CODE'
        <?php

        declare (strict_types=1);
        CODE))->toBe(<<<'CODE'
        <?php

        declare (strict_types=1);
        CODE);
});
