<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\NotEqualToEqual;

it('mutates a not equal to an equal equation', function (): void {
    expect(mutateCode(NotEqualToEqual::class, <<<'CODE'
        <?php

        if ($a != $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a == $b) {
            return true;
        }
        CODE);
});
