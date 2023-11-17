<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Removal\RemoveFunctionCall;

it('removes a function call', function (): void {
    expect(mutateCode(RemoveFunctionCall::class, <<<'CODE'
        <?php

        foo();
        CODE))->toBe(<<<'CODE'
        <?php
        
        
        CODE);
});
