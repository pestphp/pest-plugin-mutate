<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayKeys;

it('unwraps the array_keys function', function (): void {
    expect(mutateCode(UnwrapArrayKeys::class, <<<'CODE'
        <?php

        $a = array_keys(['foo' => 1, 'bar' => 2]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo' => 1, 'bar' => 2];
        CODE);
});
