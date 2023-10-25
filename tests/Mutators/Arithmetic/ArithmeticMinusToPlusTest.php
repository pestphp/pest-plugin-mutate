<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\ArithmeticMinusToPlus;

it('mutates a binary minus to plus', function () {
    expect(mutateCode(ArithmeticMinusToPlus::class, <<<'CODE'
        <?php

        1 - 1;
        CODE))->toBe(<<<'CODE'
        <?php
        
        1 + 1;
        CODE);
});
