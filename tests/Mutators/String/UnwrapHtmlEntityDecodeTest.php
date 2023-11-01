<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapHtmlEntityDecode;

it('unwraps the html_entity_decode function', function (): void {
    expect(mutateCode(UnwrapHtmlEntityDecode::class, <<<'CODE'
        <?php

        $a = html_entity_decode('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
