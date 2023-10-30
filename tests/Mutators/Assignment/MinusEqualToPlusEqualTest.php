<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Assignment\MinusEqualToPlusEqual;

it('mutates a minus equal assignment to plus equal', function (): void {
    expect(mutateCode(MinusEqualToPlusEqual::class, <<<'CODE'
        <?php

        $a -= 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a += 1;
        CODE);
});
