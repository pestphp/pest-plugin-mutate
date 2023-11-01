<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapHtmlspecialchars;

it('unwraps the htmlspecialchars function', function (): void {
    expect(mutateCode(UnwrapHtmlspecialchars::class, <<<'CODE'
        <?php

        $a = htmlspecialchars('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
