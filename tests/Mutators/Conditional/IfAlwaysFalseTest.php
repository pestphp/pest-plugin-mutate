<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Conditionals\IfAlwaysFalse;

it('mutates an if condition to always false', function (): void {
    expect(mutateCode(IfAlwaysFalse::class, <<<'CODE'
        <?php

        if ($a > $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php

        if (false) {
            return true;
        }
        CODE);
});
