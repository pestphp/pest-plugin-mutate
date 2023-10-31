<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\LogicalOrToLogicalAnd;

it('mutates logical or to logical and', function (): void {
    expect(mutateCode(LogicalOrToLogicalAnd::class, <<<'CODE'
        <?php

        if ($a or $b) {
            $this->doSomething();
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a and $b) {
            $this->doSomething();
        }
        CODE);
});
