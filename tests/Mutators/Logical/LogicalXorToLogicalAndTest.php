<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\LogicalXorToLogicalAnd;

it('mutates logical xor to logical and', function (): void {
    expect(mutateCode(LogicalXorToLogicalAnd::class, <<<'CODE'
        <?php

        if ($a xor $b) {
            $this->doSomething();
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a and $b) {
            $this->doSomething();
        }
        CODE);
});
