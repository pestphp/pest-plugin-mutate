<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayReverse;

it('unwraps the array_reverse function', function (): void {
    expect(mutateCode(UnwrapArrayReverse::class, <<<'CODE'
        <?php

        $a = array_reverse([1, 2, 3]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
