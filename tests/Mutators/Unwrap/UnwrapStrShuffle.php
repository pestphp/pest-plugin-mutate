<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Unwrap\UnwrapStrShuffle;

it('unwraps the str_shuffle function', function (): void {
    expect(mutateCode(UnwrapStrShuffle::class, <<<'CODE'
        <?php

        $a = str_shuffle('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
