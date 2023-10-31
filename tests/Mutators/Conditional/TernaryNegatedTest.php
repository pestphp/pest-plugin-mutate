<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Conditionals\TernaryNegated;

it('mutates an ternary condition to be negated', function (): void {
    expect(mutateCode(TernaryNegated::class, <<<'CODE'
        <?php

        return $a > $b ? 'a' : 'b';
        CODE))->toBe(<<<'CODE'
        <?php

        return !($a > $b) ? 'a' : 'b';
        CODE);
});
