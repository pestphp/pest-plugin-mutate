<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\EmptyStringToNotEmpty;

it('mutates an empty string to a not empty string', function (): void {
    expect(mutateCode(EmptyStringToNotEmpty::class, <<<'CODE'
        <?php

        $a = '';
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'PEST Mutator was here!';
        CODE);
});
