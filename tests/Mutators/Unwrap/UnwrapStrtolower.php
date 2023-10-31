<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Unwrap\UnwrapStrtolower;

it('unwraps the strtolower function', function (): void {
    expect(mutateCode(UnwrapStrtolower::class, <<<'CODE'
        <?php

        $a = strtolower('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
