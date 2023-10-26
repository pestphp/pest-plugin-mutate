<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Equality\SmallerToSmallerOrEqual;

it('mutates a greater equation to a greater than equation', function (): void {
    expect(mutateCode(SmallerToSmallerOrEqual::class, <<<'CODE'
        <?php

        if ($a < $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a <= $b) {
            return true;
        }
        CODE);
});
