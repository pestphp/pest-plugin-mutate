<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayMap;

it('unwraps the array_map function', function (): void {
    expect(mutateCode(UnwrapArrayMap::class, <<<'CODE'
        <?php

        $a = array_map(function ($i) {
            return $i + 1;
        }, [1, 2, 3]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
