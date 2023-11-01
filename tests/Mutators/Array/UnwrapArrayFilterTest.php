<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayFilter;

it('unwraps the array_filter function', function (): void {
    expect(mutateCode(UnwrapArrayFilter::class, <<<'CODE'
        <?php

        $a = array_filter([1, 2, '3'], 'is_string');
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, '3'];
        CODE);
});
