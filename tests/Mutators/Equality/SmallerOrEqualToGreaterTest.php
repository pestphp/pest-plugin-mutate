<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\SmallerOrEqualToGreater;

it('mutates a smaller or equal equation to a greater equation', function (): void {
    expect(mutateCode(SmallerOrEqualToGreater::class, <<<'CODE'
        <?php

        if ($a <= $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a > $b) {
            return true;
        }
        CODE);
});
