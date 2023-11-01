<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapStrReplace;

it('unwraps the str_replace function', function (): void {
    expect(mutateCode(UnwrapStrReplace::class, <<<'CODE'
        <?php

        $a = str_replace('f', 'b', 'foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
