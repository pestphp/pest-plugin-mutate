<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\NotEmptyStringToEmpty;

it('mutates a not empty string to an empty string', function (): void {
    expect(mutateCode(NotEmptyStringToEmpty::class, <<<'CODE'
        <?php

        $a = 'foo';
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = '';
        CODE);
});
