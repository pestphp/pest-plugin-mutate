<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayReduce;

it('unwraps the array_reduce function', function (): void {
    expect(mutateCode(UnwrapArrayReduce::class, <<<'CODE'
        <?php

        $a = array_reduce([1, 2, 3], fn ($carry, $item) => $carry + $item, 0);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
