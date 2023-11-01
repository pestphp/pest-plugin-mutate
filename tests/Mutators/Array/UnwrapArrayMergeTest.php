<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayMerge;

it('unwraps the array_merge function', function (): void {
    expect(mutateCode(UnwrapArrayMerge::class, <<<'CODE'
        <?php

        $a = array_merge([1, 2, 3], [4, 5]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
