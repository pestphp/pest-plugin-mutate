<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Casting\RemoveDoubleCast;

it('mutates by removing double casts', function (): void {
    expect(mutateCode(RemoveDoubleCast::class, <<<'CODE'
        <?php

        $a = (double) 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 1;
        CODE);
});
