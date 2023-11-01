<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayIntersect;

it('unwraps the array_intersect function', function (): void {
    expect(mutateCode(UnwrapArrayIntersect::class, <<<'CODE'
        <?php

        $a = array_intersect([1, 2, 3], [1, 2]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
