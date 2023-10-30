<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Assignment\MultiplyEqualToDivideEqual;

it('mutates a multiply equal assignment to divide equal', function (): void {
    expect(mutateCode(MultiplyEqualToDivideEqual::class, <<<'CODE'
        <?php

        $a *= 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a /= 1;
        CODE);
});
