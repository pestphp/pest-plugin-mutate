<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayUintersect;

it('unwraps the array_uintersect function', function (): void {
    expect(mutateCode(UnwrapArrayUintersect::class, <<<'CODE'
        <?php

        $a = array_uintersect([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
