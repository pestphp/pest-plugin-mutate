<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Array\UnwrapArrayChangeKeyCase;

it('unwraps the array_change_key_case function', function (): void {
    expect(mutateCode(UnwrapArrayChangeKeyCase::class, <<<'CODE'
        <?php

        $a = array_change_key_case(['foo' => 'bar'], CASE_UPPER);
        CODE))->toBe(<<<'CODE'
        <?php

        $a = ['foo' => 'bar'];
        CODE);
});
