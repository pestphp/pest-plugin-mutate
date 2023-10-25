<?php

declare(strict_types=1);

it('mutates an if condition to always false', function () {
    expect(mutateCode(\Pest\Mutate\Mutators\Conditionals\ConditionalsIfAlwaysTrue::class, <<<'CODE'
        <?php

        if ($a > $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if (true) {
            return true;
        }
        CODE);
});
