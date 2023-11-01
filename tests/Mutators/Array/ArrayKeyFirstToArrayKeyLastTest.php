<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\ArrayKeyFirstToArrayKeyLast;

it('mutates array_key_first to array_key_last', function (): void {
    expect(mutateCode(ArrayKeyFirstToArrayKeyLast::class, <<<'CODE'
        <?php

        $a = array_key_first(['a' => 1, 'b' => 2, 'c' => 3]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = array_key_last(['a' => 1, 'b' => 2, 'c' => 3]);
        CODE);
});
