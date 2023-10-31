<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\BooleanAndToBooleanOr;

it('mutates boolean and to boolean or', function (): void {
    expect(mutateCode(BooleanAndToBooleanOr::class, <<<'CODE'
        <?php

        if ($a && $b) {
            $this->doSomething();
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a || $b) {
            $this->doSomething();
        }
        CODE);
});
