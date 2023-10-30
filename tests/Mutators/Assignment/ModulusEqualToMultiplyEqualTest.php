<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Assignment\ModulusEqualToMultiplyEqual;

it('mutates a modulus equal assignment to multiply equal', function (): void {
    expect(mutateCode(ModulusEqualToMultiplyEqual::class, <<<'CODE'
        <?php

        $a %= 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a *= 1;
        CODE);
});
