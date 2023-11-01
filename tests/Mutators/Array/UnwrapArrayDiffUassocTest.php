<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayDiffUassoc;

it('unwraps the array_diff_uassoc function', function (): void {
    expect(mutateCode(UnwrapArrayDiffUassoc::class, <<<'CODE'
        <?php

        $a = array_diff_uassoc([1, 2, 3], [1, 2], 'strcmp');
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
