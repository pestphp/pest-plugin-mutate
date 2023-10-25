<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Conditionals\IfAlwaysTrue;

it('mutates an if condition to always false', function (): void {
    expect(mutateCode(IfAlwaysTrue::class, <<<'CODE'
        <?php

        if ($a > $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php

        if (true) {
            return true;
        }
        CODE);
});
