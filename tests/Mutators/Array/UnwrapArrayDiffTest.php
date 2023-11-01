<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayDiff;

it('unwraps the array_diff function', function (): void {
    expect(mutateCode(UnwrapArrayDiff::class, <<<'CODE'
        <?php

        $a = array_diff([1, 2, 3], [1, 2]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
