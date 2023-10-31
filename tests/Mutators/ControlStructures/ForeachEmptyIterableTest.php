<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\ControlStructures\ForeachEmptyIterable;

it('mutates a foreach statement to be empty', function (): void {
    expect(mutateCode(ForeachEmptyIterable::class, <<<'CODE'
        <?php

        foreach ([1, 2, 3] as $value) {
            echo $value;
        }
        CODE))->toBe(<<<'CODE'
        <?php

        foreach ([] as $value) {
            echo $value;
        }
        CODE);
});
