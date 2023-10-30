<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\BooleanOrToBooleanAnd;

it('mutates boolean or to boolean and', function (): void {
    expect(mutateCode(BooleanOrToBooleanAnd::class, <<<'CODE'
        <?php

        if ($a || $b) {
            $this->doSomething();
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a && $b) {
            $this->doSomething();
        }
        CODE);
});
