<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Unwrap\UnwrapHtmlspecialcharsDecode;

it('unwraps the htmlspecialchars_decode function', function (): void {
    expect(mutateCode(UnwrapHtmlspecialcharsDecode::class, <<<'CODE'
        <?php

        $a = htmlspecialchars_decode('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
