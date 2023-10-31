<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Math\RoundToCeil;

it('mutates a round to a ceil function call', function (): void {
    expect(mutateCode(RoundToCeil::class, <<<'CODE'
        <?php

        $a = round($b);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ceil($b);
        CODE);
});
