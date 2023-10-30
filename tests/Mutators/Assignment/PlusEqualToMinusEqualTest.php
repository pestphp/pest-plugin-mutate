<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Assignment\PlusEqualToMinusEqual;

it('mutates a plus equal assignment to minus equal', function (): void {
    expect(mutateCode(PlusEqualToMinusEqual::class, <<<'CODE'
        <?php

        $a += 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a -= 1;
        CODE);
});
