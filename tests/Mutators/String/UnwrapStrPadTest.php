<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapStrPad;

it('unwraps the str_pad function', function (): void {
    expect(mutateCode(UnwrapStrPad::class, <<<'CODE'
        <?php

        $a = str_pad('foo', 10, ' ');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
