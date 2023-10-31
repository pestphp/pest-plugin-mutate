<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Math\MinToMax;

it('mutates a min to a max function call', function (): void {
    expect(mutateCode(MinToMax::class, <<<'CODE'
        <?php

        $a = min(1, 2, 3);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = max(1, 2, 3);
        CODE);
});
