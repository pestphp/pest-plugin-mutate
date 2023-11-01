<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapUcwords;

it('unwraps the ucwords function', function (): void {
    expect(mutateCode(UnwrapUcwords::class, <<<'CODE'
        <?php

        $a = ucwords('foo bar');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo bar';
        CODE);
});
