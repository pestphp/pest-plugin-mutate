<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayDiffAssoc;

it('unwraps the array_diff_assoc function', function (): void {
    expect(mutateCode(UnwrapArrayDiffAssoc::class, <<<'CODE'
        <?php

        $a = array_diff_assoc(['foo' => 1, 'bar' => 2], ['foo' => 1]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo' => 1, 'bar' => 2];
        CODE);
});
