<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayChunk;

it('unwraps the array_chunk function', function (): void {
    expect(mutateCode(UnwrapArrayChunk::class, <<<'CODE'
        <?php

        $a = array_chunk([1, 2, 3], 2);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [1, 2, 3];
        CODE);
});
