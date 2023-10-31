<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\EqualToIdentical;

it('mutates an equal to identical equation', function (): void {
    expect(mutateCode(EqualToIdentical::class, <<<'CODE'
        <?php

        if ($a == $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a === $b) {
            return true;
        }
        CODE);
});
