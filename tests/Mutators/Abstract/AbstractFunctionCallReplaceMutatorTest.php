<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Math\MinToMax;

it('does not replace expression function calls', function (): void {
    expect(mutateCode(MinToMax::class, <<<'CODE'
        <?php

        $a = ($this->min)(1, 2);
        CODE))->toBe(<<<'CODE'
        <?php
        
        $a = ($this->min)(1, 2);
        CODE);
});

it('can not mutate non function call nodes', function (): void {
    expect(MinToMax::can(new \PhpParser\Node\Attribute(new \PhpParser\Node\Name('min'))))
        ->toBeFalse();
});
