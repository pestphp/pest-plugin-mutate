<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapWordwrap;

it('unwraps the wordwrap function', function (): void {
    expect(mutateCode(UnwrapWordwrap::class, <<<'CODE'
        <?php

        $a = wordwrap('foo bar', 3);
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo bar';
        CODE);
});
