<?php

declare(strict_types=1);

it('mutates an ternary condition to always true', function () {
    expect(mutateCode(\Pest\Mutate\Mutators\Conditionals\ConditionalsTernaryAlwaysTrue::class, <<<'CODE'
        <?php

        return $a > $b ? 'a' : 'b';
        CODE))->toBe(<<<'CODE'
        <?php
        
        return true ? 'a' : 'b';
        CODE);
});
