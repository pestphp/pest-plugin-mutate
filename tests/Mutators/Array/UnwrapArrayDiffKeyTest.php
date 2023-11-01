<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayDiffKey;

it('unwraps the array_diff_key function', function (): void {
    expect(mutateCode(UnwrapArrayDiffKey::class, <<<'CODE'
        <?php

        $a = array_diff_key(['foo' => 1, 'bar' => 2], ['foo' => 1]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo' => 1, 'bar' => 2];
        CODE);
});
