<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\BitwiseOrToBitwiseAnd;

it('mutates a bitwise or to a bitwise and', function (): void {
    expect(mutateCode(BitwiseOrToBitwiseAnd::class, <<<'CODE'
        <?php

        $a = $b | $c;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = $b & $c;
        CODE);
});
