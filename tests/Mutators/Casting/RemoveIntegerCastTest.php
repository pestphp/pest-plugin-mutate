<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Casting\RemoveIntegerCast;

it('mutates by removing integer casts', function (): void {
    expect(mutateCode(RemoveIntegerCast::class, <<<'CODE'
        <?php

        $a = (int) 1.0;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 1.0;
        CODE);
});
