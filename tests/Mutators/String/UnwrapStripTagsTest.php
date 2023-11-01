<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapStripTags;

it('unwraps the strip_tags function', function (): void {
    expect(mutateCode(UnwrapStripTags::class, <<<'CODE'
        <?php

        $a = strip_tags('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
