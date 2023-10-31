<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\NotEqualToNotIdentical;

it('mutates a not equal to a not identical equation', function (): void {
    expect(mutateCode(NotEqualToNotIdentical::class, <<<'CODE'
        <?php

        if ($a != $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a !== $b) {
            return true;
        }
        CODE);
});
