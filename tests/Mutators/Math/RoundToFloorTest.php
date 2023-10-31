<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Math\RoundToFloor;

it('mutates a round to a floor function call', function (): void {
    expect(mutateCode(RoundToFloor::class, <<<'CODE'
        <?php

        $a = round($b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = floor($b);
        CODE);
});
