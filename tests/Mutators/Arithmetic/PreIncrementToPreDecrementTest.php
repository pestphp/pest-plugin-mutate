<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\PreIncrementToPreDecrement;

it('mutates a pre increment operation to pre decrement', function (): void {
    expect(mutateCode(PreIncrementToPreDecrement::class, <<<'CODE'
        <?php

        return ++$a;
        CODE))->toBe(<<<'CODE'
        <?php

        return --$a;
        CODE);
});
