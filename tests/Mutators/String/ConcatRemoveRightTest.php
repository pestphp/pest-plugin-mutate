<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\ConcatRemoveRight;

it('mutates a coalesce operation by removing the right side', function (): void {
    expect(mutateCode(ConcatRemoveRight::class, <<<'CODE'
        <?php

        $a = $b . $c;
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = $b;
        CODE);
});
