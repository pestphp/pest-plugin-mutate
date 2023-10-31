<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\NotIdenticalToNotEqual;

it('mutates a not identical to a not equal equation', function (): void {
    expect(mutateCode(NotIdenticalToNotEqual::class, <<<'CODE'
        <?php

        if ($a !== $b) {
            return true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        if ($a != $b) {
            return true;
        }
        CODE);
});
