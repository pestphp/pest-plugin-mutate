<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayUintersectAssoc;

it('unwraps the array_uintersect_assoc function', function (): void {
    expect(mutateCode(UnwrapArrayUintersectAssoc::class, <<<'CODE'
        <?php

        $a = array_uintersect_assoc([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
