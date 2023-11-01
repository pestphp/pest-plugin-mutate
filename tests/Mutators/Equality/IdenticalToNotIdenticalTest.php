<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\IdenticalToNotIdentical;

it('mutates an identical to a not identical equation', function (): void {
    expect(mutateCode(IdenticalToNotIdentical::class, <<<'CODE'
        <?php

        if ($a === $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a !== $b) {
            return true;
        }
        CODE);
});
