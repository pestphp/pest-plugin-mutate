<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\IdenticalToEqual;

it('mutates an identical to equal equation', function (): void {
    expect(mutateCode(IdenticalToEqual::class, <<<'CODE'
        <?php

        if ($a === $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a == $b) {
            return true;
        }
        CODE);
});
