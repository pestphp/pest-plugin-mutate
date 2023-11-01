<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayPad;

it('unwraps the array_pad function', function (): void {
    expect(mutateCode(UnwrapArrayPad::class, <<<'CODE'
        <?php

        $a = array_pad([1, 2, 3], 5, 0);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
