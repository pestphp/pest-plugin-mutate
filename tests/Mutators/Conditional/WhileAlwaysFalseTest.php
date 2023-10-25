<?php

declare(strict_types=1);

it('mutates a while condition to always false', function () {
    expect(mutateCode(\Pest\Mutate\Mutators\Conditionals\WhileAlwaysFalse::class, <<<'CODE'
        <?php

        while ($a > $b) {
            $b++;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        while (false) {
            $b++;
        }
        CODE);
});
