<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayColumn;

it('unwraps the array_column function', function (): void {
    expect(mutateCode(UnwrapArrayColumn::class, <<<'CODE'
        <?php

        $a = array_column([['id' => 1], ['id' => 2]], 'id');
        CODE))->toBe(<<<'CODE'
        <?php

        $a = [['id' => 1], ['id' => 2]];
        CODE);
});
