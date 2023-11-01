<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayFlip;

it('unwraps the array_flip function', function (): void {
    expect(mutateCode(UnwrapArrayFlip::class, <<<'CODE'
        <?php

        $a = array_flip(['foo' => 1, 'bar' => 2]);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo' => 1, 'bar' => 2];
        CODE);
});
