<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Math\CeilToFloor;

it('mutates a ceil to a floor function call', function (): void {
    expect(mutateCode(CeilToFloor::class, <<<'CODE'
        <?php

        $a = ceil($b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = floor($b);
        CODE);
});
