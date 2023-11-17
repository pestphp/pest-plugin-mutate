<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\ControlStructures\WhileAlwaysFalse;

it('mutates a while condition to always false', function (): void {
    expect(mutateCode(WhileAlwaysFalse::class, <<<'CODE'
        <?php

        while ($a > $b) {
            $b++;
        }
        CODE))->toBe(<<<'CODE'
        <?php

        while (false) {
            $b++;
        }
        CODE);
});

it('does not mutate other statements', function (): void {
    mutateCode(WhileAlwaysFalse::class, <<<'CODE'
        <?php

        if ($a > $b) {
            $b++;
        }
        CODE);
})->expectExceptionMessage('No mutation performed');
