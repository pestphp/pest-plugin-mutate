<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Assignment\CoalesceEqualToEqual;

it('mutates a plus equal assignment to minus equal', function (): void {
    expect(mutateCode(CoalesceEqualToEqual::class, <<<'CODE'
        <?php

        $a ??= $b;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = $b;
        CODE);
});
