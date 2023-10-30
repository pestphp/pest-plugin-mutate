<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\ModulusToMultiplication;

it('mutates a binary modulus to multiplication', function (): void {
    expect(mutateCode(ModulusToMultiplication::class, <<<'CODE'
        <?php

        1 % 1;
        CODE))->toBe(<<<'CODE'
        <?php

        1 * 1;
        CODE);
});
