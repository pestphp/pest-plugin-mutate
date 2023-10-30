<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Assignment\DivideEqualToMultiplyEqual;

it('mutates a divide equal assignment to multiply equal', function (): void {
    expect(mutateCode(DivideEqualToMultiplyEqual::class, <<<'CODE'
        <?php

        $a /= 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a *= 1;
        CODE);
});
