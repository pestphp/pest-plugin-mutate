<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapHtmlentities;

it('unwraps the htmlentities function', function (): void {
    expect(mutateCode(UnwrapHtmlentities::class, <<<'CODE'
        <?php

        $a = htmlentities('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
