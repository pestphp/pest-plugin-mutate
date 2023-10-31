<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Equality\SmallerToGreaterOrEqual;

it('mutates a smaller equation to a greater or equal equation', function (): void {
    expect(mutateCode(SmallerToGreaterOrEqual::class, <<<'CODE'
        <?php

        if ($a < $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a >= $b) {
            return true;
        }
        CODE);
});
