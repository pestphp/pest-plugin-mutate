<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Math\FloorToCiel;

it('mutates a floor to a ceil function call', function (): void {
    expect(mutateCode(FloorToCiel::class, <<<'CODE'
        <?php

        $a = floor($b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ceil($b);
        CODE);
});
