<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\EqualToNotEqual;

it('mutates an equal to not equal equation', function (): void {
    expect(mutateCode(EqualToNotEqual::class, <<<'CODE'
        <?php

        if ($a == $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a != $b) {
            return true;
        }
        CODE);
});
