<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\MultiplicationToDivision;

it('mutates a binary multiplication to division', function (): void {
    expect(mutateCode(MultiplicationToDivision::class, <<<'CODE'
        <?php

        1 * 1;
        CODE))->toBe(<<<'CODE'
        <?php

        1 / 1;
        CODE);
});
