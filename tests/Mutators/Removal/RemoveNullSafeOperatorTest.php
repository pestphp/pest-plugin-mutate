<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Removal\RemoveNullSafeOperator;

it('mutates a null safe method call to a regular method call', function (): void {
    expect(mutateCode(RemoveNullSafeOperator::class, <<<'CODE'
        <?php

        return $a?->b();
        CODE))->toBe(<<<'CODE'
        <?php
        
        return $a->b();
        CODE);
});

it('mutates a null safe property call to a regular property call', function (): void {
    expect(mutateCode(RemoveNullSafeOperator::class, <<<'CODE'
        <?php

        return $a?->b;
        CODE))->toBe(<<<'CODE'
        <?php
        
        return $a->b;
        CODE);
});
