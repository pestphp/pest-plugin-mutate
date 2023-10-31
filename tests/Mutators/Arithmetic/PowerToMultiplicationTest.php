<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\PowerToMultiplication;

it('mutates a binary power to multiplication', function (): void {
    expect(mutateCode(PowerToMultiplication::class, <<<'CODE'
        <?php

        1 ** 1;
        CODE))->toBe(<<<'CODE'
        <?php

        1 * 1;
        CODE);
});
