<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\StrEndsWithToStrStartsWith;

it('mutates str_ends_with to str_starts_with', function (): void {
    expect(mutateCode(StrEndsWithToStrStartsWith::class, <<<'CODE'
        <?php

        $a = str_ends_with('foo', 'bar');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = str_starts_with('foo', 'bar');
        CODE);
});
