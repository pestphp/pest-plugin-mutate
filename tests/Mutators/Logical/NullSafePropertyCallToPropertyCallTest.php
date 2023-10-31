<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Logical\NullSafePropertyCallToPropertyCall;

it('mutates a null safe property call to a regular property call', function (): void {
    expect(mutateCode(NullSafePropertyCallToPropertyCall::class, <<<'CODE'
        <?php

        return $a?->b;
        CODE))->toBe(<<<'CODE'
        <?php
        
        return $a->b;
        CODE);
});
