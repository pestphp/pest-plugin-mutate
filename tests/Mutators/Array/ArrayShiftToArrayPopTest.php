<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Array\ArrayShiftToArrayPop;

it('mutates array_shift to array_pop', function (): void {
    expect(mutateCode(ArrayShiftToArrayPop::class, <<<'CODE'
        <?php

        $a = array_shift([1, 2, 3]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = array_pop([1, 2, 3]);
        CODE);
});
