<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Logical\NullSafeMethodCallToMethodCall;

it('mutates a null safe method call to a regular method call', function (): void {
    expect(mutateCode(NullSafeMethodCallToMethodCall::class, <<<'CODE'
        <?php

        return $a?->b();
        CODE))->toBe(<<<'CODE'
        <?php
        
        return $a->b();
        CODE);
});
