<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\TrueToFalse;

it('mutates a true to a false', function (): void {
    expect(mutateCode(TrueToFalse::class, <<<'CODE'
        <?php

        return true;
        CODE))->toBe(<<<'CODE'
        <?php
        
        return false;
        CODE);
});
