<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\CoalesceRemoveLeft;

it('mutates a coalesce operation by replacing it with the right element', function (): void {
    expect(mutateCode(CoalesceRemoveLeft::class, <<<'CODE'
        <?php

        $a = $b ?? $c;
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = $c;
        CODE);
});
