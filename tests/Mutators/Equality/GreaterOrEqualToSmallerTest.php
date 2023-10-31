<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Equality\GreaterOrEqualToSmaller;

it('mutates a greater or equal equation to a smaller than equation', function (): void {
    expect(mutateCode(GreaterOrEqualToSmaller::class, <<<'CODE'
        <?php

        if ($a >= $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a < $b) {
            return true;
        }
        CODE);
});
