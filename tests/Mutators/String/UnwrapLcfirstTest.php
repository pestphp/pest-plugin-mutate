<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapLcfirst;

it('unwraps the lcfirst function', function (): void {
    expect(mutateCode(UnwrapLcfirst::class, <<<'CODE'
        <?php

        $a = lcfirst('Foo');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = 'Foo';
        CODE);
});
