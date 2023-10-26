<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Equality\GreaterOrEqualToGreater;

it('mutates a greater equation to a greater than equation', function (): void {
    expect(mutateCode(GreaterOrEqualToGreater::class, <<<'CODE'
        <?php

        if ($a >= $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a > $b) {
            return true;
        }
        CODE);
});
