<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapMd5;

it('unwraps the md5 function', function (): void {
    expect(mutateCode(UnwrapMd5::class, <<<'CODE'
        <?php

        $a = md5('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
