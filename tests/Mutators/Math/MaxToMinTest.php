<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Math\MaxToMin;

it('mutates a max to a min function call', function (): void {
    expect(mutateCode(MaxToMin::class, <<<'CODE'
        <?php

        $a = max(1, 2, 3);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = min(1, 2, 3);
        CODE);
});
