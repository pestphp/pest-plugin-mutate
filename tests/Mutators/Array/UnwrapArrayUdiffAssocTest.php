<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayUdiffAssoc;

it('unwraps the array_udiff_assoc function', function (): void {
    expect(mutateCode(UnwrapArrayUdiffAssoc::class, <<<'CODE'
        <?php

        $a = array_udiff_assoc([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
