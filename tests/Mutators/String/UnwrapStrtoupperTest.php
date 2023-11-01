<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapStrtoupper;

it('unwraps the strtoupper function', function (): void {
    expect(mutateCode(UnwrapStrtoupper::class, <<<'CODE'
        <?php

        $a = strtoupper('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
