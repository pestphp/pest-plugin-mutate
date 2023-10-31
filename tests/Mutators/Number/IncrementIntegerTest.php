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
