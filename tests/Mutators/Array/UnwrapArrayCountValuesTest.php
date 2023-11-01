<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayCountValues;

it('unwraps the array_count_values function', function (): void {
    expect(mutateCode(UnwrapArrayCountValues::class, <<<'CODE'
        <?php

        $a = array_count_values(['foo', 'foo', 'bar']);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo', 'foo', 'bar'];
        CODE);
});
