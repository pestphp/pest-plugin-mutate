<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapTrim;

it('unwraps the trim function', function (): void {
    expect(mutateCode(UnwrapTrim::class, <<<'CODE'
        <?php

        $a = trim('foo ');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo ';
        CODE);
});
