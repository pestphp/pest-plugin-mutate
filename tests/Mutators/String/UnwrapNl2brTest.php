<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapNl2br;

it('unwraps the nl2br function', function (): void {
    expect(mutateCode(UnwrapNl2br::class, <<<'CODE'
        <?php

        $a = nl2br('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
