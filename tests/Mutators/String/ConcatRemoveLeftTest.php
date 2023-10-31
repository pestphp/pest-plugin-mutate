<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\ConcatRemoveLeft;

it('mutates a coalesce operation by removing the left side', function (): void {
    expect(mutateCode(ConcatRemoveLeft::class, <<<'CODE'
        <?php

        $a = $b . $c;
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = $c;
        CODE);
});
