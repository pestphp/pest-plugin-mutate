<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArraySplice;

it('unwraps the array_splice function', function (): void {
    expect(mutateCode(UnwrapArraySplice::class, <<<'CODE'
        <?php

        $b = array_splice($a, 0, 2, [1, 2]);
        CODE))->toBe(<<<'CODE'
        <?php

        $b = $a;
        CODE);
});
