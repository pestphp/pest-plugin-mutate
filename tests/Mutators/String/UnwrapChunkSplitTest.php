<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapChunkSplit;

it('unwraps the chunk_split function', function (): void {
    expect(mutateCode(UnwrapChunkSplit::class, <<<'CODE'
        <?php

        $a = chunk_split('foo', 2, PHP_EOL);
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
