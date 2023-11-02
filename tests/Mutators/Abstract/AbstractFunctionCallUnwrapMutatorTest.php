<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapChop;

it('does not unwrap expression function calls', function (): void {
    expect(mutateCode(UnwrapChop::class, <<<'CODE'
        <?php

        $a = ($this->chop)('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = ($this->chop)('foo');
        CODE);
});
