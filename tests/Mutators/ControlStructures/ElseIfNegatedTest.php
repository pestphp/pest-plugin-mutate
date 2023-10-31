<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\ControlStructures\ElseIfNegated;

it('mutates an else if condition to be negated', function (): void {
    expect(mutateCode(ElseIfNegated::class, <<<'CODE'
        <?php

        if ($a > $b) {
            return 1;
        } elseif ($a > $c) {
            return 2;
        }
        CODE))->toBe(<<<'CODE'
        <?php

        if ($a > $b) {
            return 1;
        } elseif (!($a > $c)) {
            return 2;
        }
        CODE);
});
