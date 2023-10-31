<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Unwrap\UnwrapChop;

it('unwraps the chop function', function (): void {
    expect(mutateCode(UnwrapChop::class, <<<'CODE'
        <?php

        $a = chop('foo', 'f');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
