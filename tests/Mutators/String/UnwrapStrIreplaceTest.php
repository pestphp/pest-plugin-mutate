<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapStrIreplace;

it('unwraps the str_ireplace function', function (): void {
    expect(mutateCode(UnwrapStrIreplace::class, <<<'CODE'
        <?php

        $a = str_ireplace('f', 'b', 'foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
