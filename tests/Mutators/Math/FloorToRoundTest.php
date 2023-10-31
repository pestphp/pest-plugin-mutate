<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Math\FloorToRound;

it('mutates a floor to a round function call', function (): void {
    expect(mutateCode(FloorToRound::class, <<<'CODE'
        <?php

        $a = floor($b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = round($b);
        CODE);
});
