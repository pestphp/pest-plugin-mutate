<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapSubstr;

it('unwraps the substr function', function (): void {
    expect(mutateCode(UnwrapSubstr::class, <<<'CODE'
        <?php

        $a = substr('foo', 0, 1);
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
