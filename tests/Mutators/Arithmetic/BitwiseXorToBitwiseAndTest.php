<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Arithmetic\BitwiseXorToBitwiseAnd;

it('mutates a bitwise xor to a bitwise and', function (): void {
    expect(mutateCode(BitwiseXorToBitwiseAnd::class, <<<'CODE'
        <?php

        $a = $b ^ $c;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = $b & $c;
        CODE);
});
