<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapRtrim;

it('unwraps the rtrim function', function (): void {
    expect(mutateCode(UnwrapRtrim::class, <<<'CODE'
        <?php

        $a = rtrim('foo', 'f');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
