<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Assignment\ShiftRightToShiftLeft;

it('mutates a shift right assignment to shift left', function (): void {
    expect(mutateCode(ShiftRightToShiftLeft::class, <<<'CODE'
        <?php

        $a >>= 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a <<= 1;
        CODE);
});
