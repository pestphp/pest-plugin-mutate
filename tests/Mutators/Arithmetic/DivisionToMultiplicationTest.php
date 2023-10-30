<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\DivisionToMultiplication;

it('mutates a binary division to multiplication', function (): void {
    expect(mutateCode(DivisionToMultiplication::class, <<<'CODE'
        <?php

        1 / 1;
        CODE))->toBe(<<<'CODE'
        <?php

        1 * 1;
        CODE);
});
