<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayReplaceRecursive;

it('unwraps the array_replace function', function (): void {
    expect(mutateCode(UnwrapArrayReplaceRecursive::class, <<<'CODE'
        <?php

        $a = array_replace_recursive([1, 2, 3], ['a', 'b', 'c']);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
