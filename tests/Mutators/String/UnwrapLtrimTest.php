<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapLtrim;

it('unwraps the ltrim function', function (): void {
    expect(mutateCode(UnwrapLtrim::class, <<<'CODE'
        <?php

        $a = ltrim('foo', 'o');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
