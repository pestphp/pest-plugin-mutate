<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayUdiff;

it('unwraps the array_udiff function', function (): void {
    expect(mutateCode(UnwrapArrayUdiff::class, <<<'CODE'
        <?php

        $a = array_udiff([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
