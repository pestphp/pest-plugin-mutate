<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Assignment\BitwiseOrToBitwiseAnd;

it('mutates a bitwise or assignment to bitwise and', function (): void {
    expect(mutateCode(BitwiseOrToBitwiseAnd::class, <<<'CODE'
        <?php

        $a |= $b;
        CODE))->toBe(<<<'CODE'
        <?php

        $a &= $b;
        CODE);
});
