<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\FalseToTrue;

it('mutates a false to a true', function (): void {
    expect(mutateCode(FalseToTrue::class, <<<'CODE'
        <?php

        return false;
        CODE))->toBe(<<<'CODE'
        <?php
        
        return true;
        CODE);
});
