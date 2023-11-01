<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayReplace;

it('unwraps the array_replace function', function (): void {
    expect(mutateCode(UnwrapArrayReplace::class, <<<'CODE'
        <?php

        $a = array_replace([1, 2, 3], ['a', 'b', 'c']);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
