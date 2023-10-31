<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Conditionals\IfNegated;

it('mutates an if condition to be negated', function (): void {
    expect(mutateCode(IfNegated::class, <<<'CODE'
        <?php

        if ($a > $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php

        if (!($a > $b)) {
            return true;
        }
        CODE);
});
