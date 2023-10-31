<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Casting\RemoveBooleanCast;

it('mutates by removing boolean casts', function (): void {
    expect(mutateCode(RemoveBooleanCast::class, <<<'CODE'
        <?php

        $a = (bool) 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 1;
        CODE);
});
