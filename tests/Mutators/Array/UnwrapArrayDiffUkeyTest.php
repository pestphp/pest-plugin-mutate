<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayDiffUkey;

it('unwraps the array_diff_ukey function', function (): void {
    expect(mutateCode(UnwrapArrayDiffUkey::class, <<<'CODE'
        <?php

        $a = array_diff_ukey(['foo' => 1, 'bar' => 2], ['foo' => 1], 'strcmp');
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo' => 1, 'bar' => 2];
        CODE);
});
