<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Assignment\ShiftLeftToShiftRight;

it('mutates a shift left assignment to shift right', function (): void {
    expect(mutateCode(ShiftLeftToShiftRight::class, <<<'CODE'
        <?php

        $a <<= 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a >>= 1;
        CODE);
});
