<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\ControlStructures\DoWhileAlwaysFalse;

it('mutates a do while condition to always false', function (): void {
    expect(mutateCode(DoWhileAlwaysFalse::class, <<<'CODE'
        <?php

        do {
            $b++;
        } while ($a > $b);
        CODE))->toBe(<<<'CODE'
        <?php

        do {
            $b++;
        } while (false);
        CODE);
});

it('does not mutate other statements', function (): void {
    mutateCode(DoWhileAlwaysFalse::class, <<<'CODE'
        <?php

        if ($a > $b) {
            $b++;
        }
        CODE);
})->expectExceptionMessage('No mutation performed');
