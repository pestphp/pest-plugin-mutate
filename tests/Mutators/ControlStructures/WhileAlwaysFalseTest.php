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
