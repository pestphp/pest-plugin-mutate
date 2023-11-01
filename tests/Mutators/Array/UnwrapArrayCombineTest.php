<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayCombine;

it('unwraps the array_combine function', function (): void {
    expect(mutateCode(UnwrapArrayCombine::class, <<<'CODE'
        <?php

        $a = array_combine(['foo'], ['bar']);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo'];
        CODE);
});
