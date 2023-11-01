<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayUdiffUassoc;

it('unwraps the array_udiff_uassoc function', function (): void {
    expect(mutateCode(UnwrapArrayUdiffUassoc::class, <<<'CODE'
        <?php

        $a = array_udiff_uassoc([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b, fn ($a, $b) => $a <=> $b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
