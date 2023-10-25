<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Conditionals\TernaryAlwaysFalse;

it('mutates an ternary condition to always false', function (): void {
    expect(mutateCode(TernaryAlwaysFalse::class, <<<'CODE'
        <?php

        return $a > $b ? 'a' : 'b';
        CODE))->toBe(<<<'CODE'
        <?php

        return false ? 'a' : 'b';
        CODE);
});
