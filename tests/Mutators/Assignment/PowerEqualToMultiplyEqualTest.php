<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Assignment\PowerEqualToMultiplyEqual;

it('mutates a power equal assignment to multiply equal', function (): void {
    expect(mutateCode(PowerEqualToMultiplyEqual::class, <<<'CODE'
        <?php

        $a **= 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a *= 1;
        CODE);
});
