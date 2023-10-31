<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\ShiftRightToShiftLeft;

it('mutates a shift right to a shift left', function (): void {
    expect(mutateCode(ShiftRightToShiftLeft::class, <<<'CODE'
        <?php

        $a >> $b;
        CODE))->toBe(<<<'CODE'
        <?php

        $a << $b;
        CODE);
});
