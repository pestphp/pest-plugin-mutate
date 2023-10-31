<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\MinusToPlus;

it('mutates a binary minus to plus', function (): void {
    expect(mutateCode(MinusToPlus::class, <<<'CODE'
        <?php

        1 - 1;
        CODE))->toBe(<<<'CODE'
        <?php

        1 + 1;
        CODE);
});
