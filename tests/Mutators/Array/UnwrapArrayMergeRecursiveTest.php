<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayMergeRecursive;

it('unwraps the array_merge_recursive function', function (): void {
    expect(mutateCode(UnwrapArrayMergeRecursive::class, <<<'CODE'
        <?php

        $a = array_merge_recursive([1, 2, 3], [4, 5]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
