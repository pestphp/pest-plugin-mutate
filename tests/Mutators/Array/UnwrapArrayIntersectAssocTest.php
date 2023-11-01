<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayIntersectAssoc;

it('unwraps the array_intersect_assoc function', function (): void {
    expect(mutateCode(UnwrapArrayIntersectAssoc::class, <<<'CODE'
        <?php

        $a = array_intersect_assoc(['foo' => 1, 'bar' => 2], ['foo' => 1]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo' => 1, 'bar' => 2];
        CODE);
});
