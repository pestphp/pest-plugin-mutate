<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\ShiftLeftToShiftRight;

it('mutates a shift left to a shift right', function (): void {
    expect(mutateCode(ShiftLeftToShiftRight::class, <<<'CODE'
        <?php

        $a << $b;
        CODE))->toBe(<<<'CODE'
        <?php

        $a >> $b;
        CODE);
});
