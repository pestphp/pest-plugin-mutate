<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\PlusToMinus;

it('mutates a binary plus to minus', function (): void {
    expect(mutateCode(PlusToMinus::class, <<<'CODE'
        <?php

        1 + 1;
        CODE))->toBe(<<<'CODE'
        <?php

        1 - 1;
        CODE);
});
