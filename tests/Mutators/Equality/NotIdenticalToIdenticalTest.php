<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Equality\NotIdenticalToIdentical;

it('mutates a not identical to an identical equation', function (): void {
    expect(mutateCode(NotIdenticalToIdentical::class, <<<'CODE'
        <?php

        if ($a !== $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a === $b) {
            return true;
        }
        CODE);
});
