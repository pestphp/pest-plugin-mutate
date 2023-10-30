<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\BitwiseAndToBitwiseOr;

it('mutates a bitwise and to a bitwise or', function (): void {
    expect(mutateCode(BitwiseAndToBitwiseOr::class, <<<'CODE'
        <?php

        $a = $b & $c;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = $b | $c;
        CODE);
});
