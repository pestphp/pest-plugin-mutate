<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArraySlice;

it('unwraps the array_slice function', function (): void {
    expect(mutateCode(UnwrapArraySlice::class, <<<'CODE'
        <?php

        $a = array_slice([1, 2, 3], 0, 2);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
