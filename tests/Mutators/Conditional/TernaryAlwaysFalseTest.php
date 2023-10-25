<?php

declare(strict_types=1);

it('mutates an ternary condition to always false', function () {
    expect(mutateCode(\Pest\Mutate\Mutators\Conditionals\TernaryAlwaysFalse::class, <<<'CODE'
        <?php

        return $a > $b ? 'a' : 'b';
        CODE))->toBe(<<<'CODE'
        <?php
        
        return false ? 'a' : 'b';
        CODE);
});
