<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayIntersectUassoc;

it('unwraps the array_intersect_uassoc function', function (): void {
    expect(mutateCode(UnwrapArrayIntersectUassoc::class, <<<'CODE'
        <?php

        $a = array_intersect_uassoc([1, 2, 3], [1, 2], 'strcmp');
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
