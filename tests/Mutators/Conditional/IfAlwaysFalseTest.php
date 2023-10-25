<?php

declare(strict_types=1);

it('mutates an if condition to always false', function () {
    expect(mutateCode(\Pest\Mutate\Mutators\Conditionals\IfAlwaysFalse::class, <<<'CODE'
        <?php

        if ($a > $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if (false) {
            return true;
        }
        CODE);
});
