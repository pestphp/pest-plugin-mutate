<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Assignment\BitwiseAndToBitwiseOr;

it('mutates a bitwise and assignment to bitwise or', function (): void {
    expect(mutateCode(BitwiseAndToBitwiseOr::class, <<<'CODE'
        <?php

        $a &= $b;
        CODE))->toBe(<<<'CODE'
        <?php

        $a |= $b;
        CODE);
});
