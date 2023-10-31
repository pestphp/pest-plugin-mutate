<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\PreDecrementToPreIncrement;

it('mutates a pre decrement operation to pre increment', function (): void {
    expect(mutateCode(PreDecrementToPreIncrement::class, <<<'CODE'
        <?php

        return --$a;
        CODE))->toBe(<<<'CODE'
        <?php

        return ++$a;
        CODE);
});
