<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Casting\RemoveArrayCast;

it('mutates by removing array casts', function (): void {
    expect(mutateCode(RemoveArrayCast::class, <<<'CODE'
        <?php

        $a = (array) 1;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 1;
        CODE);
});
