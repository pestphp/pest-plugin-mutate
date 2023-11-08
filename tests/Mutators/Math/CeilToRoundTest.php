<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Math\CeilToRound;

it('mutates a ceil to a round function call', function (): void {
    expect(mutateCode(CeilToRound::class, <<<'CODE'
        <?php

        $a = ceil($b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = round($b);
        CODE);
});
