<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Casting\RemoveObjectCast;

it('mutates by removing object casts', function (): void {
    expect(mutateCode(RemoveObjectCast::class, <<<'CODE'
        <?php

        $a = (object) ['a' => 1];
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['a' => 1];
        CODE);
});
