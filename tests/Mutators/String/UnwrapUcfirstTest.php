<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapUcfirst;

it('unwraps the ucfirst function', function (): void {
    expect(mutateCode(UnwrapUcfirst::class, <<<'CODE'
        <?php

        $a = ucfirst('foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'foo';
        CODE);
});
