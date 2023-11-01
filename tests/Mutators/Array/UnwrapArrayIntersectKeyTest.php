<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayIntersectKey;

it('unwraps the array_intersect_key function', function (): void {
    expect(mutateCode(UnwrapArrayIntersectKey::class, <<<'CODE'
        <?php

        $a = array_intersect_key(['foo' => 1, 'bar' => 2], ['foo' => 1]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo' => 1, 'bar' => 2];
        CODE);
});
