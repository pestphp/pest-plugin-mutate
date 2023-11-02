<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\StrStartsWithToStrEndsWith;

it('mutates str_starts_with to str_ends_with', function (): void {
    expect(mutateCode(StrStartsWithToStrEndsWith::class, <<<'CODE'
        <?php

        $a = str_starts_with('foo', 'bar');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = str_ends_with('foo', 'bar');
        CODE);
});
