<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\RemoveNot;

it('removes the not', function (): void {
    expect(mutateCode(RemoveNot::class, <<<'CODE'
        <?php

        return !$a;
        CODE))->toBe(<<<'CODE'
        <?php
        
        return $a;
        CODE);
});
