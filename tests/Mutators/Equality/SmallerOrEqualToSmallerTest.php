<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\SmallerOrEqualToSmaller;

it('mutates a smaller or equal equation to a smaller equation', function (): void {
    expect(mutateCode(SmallerOrEqualToSmaller::class, <<<'CODE'
        <?php

        if ($a <= $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a < $b) {
            return true;
        }
        CODE);
});
