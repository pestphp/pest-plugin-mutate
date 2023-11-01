<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapStrRepeat;

it('unwraps the str_repeat function', function (): void {
    expect(mutateCode(UnwrapStrRepeat::class, <<<'CODE'
        <?php

        $a = str_repeat('foo', 3);
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
