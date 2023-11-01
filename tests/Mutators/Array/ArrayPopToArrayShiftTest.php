<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\ArrayPopToArrayShift;

it('mutates array_pop to array_shift', function (): void {
    expect(mutateCode(ArrayPopToArrayShift::class, <<<'CODE'
        <?php

        $a = array_pop([1, 2, 3]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = array_shift([1, 2, 3]);
        CODE);
});
