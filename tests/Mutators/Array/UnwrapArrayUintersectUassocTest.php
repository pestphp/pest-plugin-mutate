<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayUintersectUassoc;

it('unwraps the array_uintersect_uassoc function', function (): void {
    expect(mutateCode(UnwrapArrayUintersectUassoc::class, <<<'CODE'
        <?php

        $a = array_uintersect_uassoc([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b, fn ($a, $b) => $a <=> $b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
