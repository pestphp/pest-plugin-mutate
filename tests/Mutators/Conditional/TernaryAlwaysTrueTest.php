<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Conditionals\TernaryAlwaysTrue;

it('mutates an ternary condition to always true', function (): void {
    expect(mutateCode(TernaryAlwaysTrue::class, <<<'CODE'
        <?php

        return $a > $b ? 'a' : 'b';
        CODE))->toBe(<<<'CODE'
        <?php

        return true ? 'a' : 'b';
        CODE);
});
