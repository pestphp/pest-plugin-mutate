<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayUnique;

it('unwraps the array_unique function', function (): void {
    expect(mutateCode(UnwrapArrayUnique::class, <<<'CODE'
        <?php

        $a = array_unique([1, 2, 2]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 2];
        CODE);
});
