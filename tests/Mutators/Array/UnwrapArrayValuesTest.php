<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayValues;

it('unwraps the array_values function', function (): void {
    expect(mutateCode(UnwrapArrayValues::class, <<<'CODE'
        <?php

        $a = array_values(['a' => 1, 'b' => 2, 'c' => 3]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['a' => 1, 'b' => 2, 'c' => 3];
        CODE);
});
