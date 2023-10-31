<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Equality\GreaterToSmallerOrEqual;

it('mutates a greater equation to a smaller or equal equation', function (): void {
    expect(mutateCode(GreaterToSmallerOrEqual::class, <<<'CODE'
        <?php

        if ($a > $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a <= $b) {
            return true;
        }
        CODE);
});
