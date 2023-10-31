<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Unwrap\UnwrapStrrev;

it('unwraps the strrev function', function (): void {
    expect(mutateCode(UnwrapStrrev::class, <<<'CODE'
        <?php

        $a = strrev('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
