<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Casting\RemoveStringCast;

it('mutates by removing string casts', function (): void {
    expect(mutateCode(RemoveStringCast::class, <<<'CODE'
        <?php

        $a = (string) 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 1;
        CODE);
});
