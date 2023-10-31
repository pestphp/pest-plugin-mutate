<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\LogicalAndToLogicalOr;

it('mutates logical and to logical or', function (): void {
    expect(mutateCode(LogicalAndToLogicalOr::class, <<<'CODE'
        <?php

        if ($a and $b) {
            $this->doSomething();
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a or $b) {
            $this->doSomething();
        }
        CODE);
});
