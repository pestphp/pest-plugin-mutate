<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Assignment\BitwiseXorToBitwiseAnd;

it('mutates a bitwise xor assignment to bitwise and', function (): void {
    expect(mutateCode(BitwiseXorToBitwiseAnd::class, <<<'CODE'
        <?php

        $a ^= $b;
        CODE))->toBe(<<<'CODE'
        <?php

        $a &= $b;
        CODE);
});
