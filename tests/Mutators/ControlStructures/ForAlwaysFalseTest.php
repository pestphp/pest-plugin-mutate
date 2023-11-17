<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\ControlStructures\ForAlwaysFalse;

it('mutates a for statement to be always false', function (): void {
    expect(mutateCode(ForAlwaysFalse::class, <<<'CODE'
        <?php

        for ($i = 0; $i < 10; $i++) {
            echo $i;
        }
        CODE))->toBe(<<<'CODE'
        <?php

        for ($i = 0; false; $i++) {
            echo $i;
        }
        CODE);
});

it('does not mutate other statements', function (): void {
    mutateCode(ForAlwaysFalse::class, <<<'CODE'
        <?php

        if ($a > $b) {
            $b++;
        }
        CODE);
})->expectExceptionMessage('No mutation performed');
