<?php

declare(strict_types=1);

it('mutates a binary plus to minus', function () {
    expect(mutateCode(\Pest\Mutate\Mutators\Arithmetic\PlusToMinus::class, <<<'CODE'
        <?php

        1 + 1;
        CODE))->toBe(<<<'CODE'
        <?php
        
        1 - 1;
        CODE);
});
