<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Math\MinToMax;
use PhpParser\Node\Attribute;
use PhpParser\Node\Name;

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
    expect(MinToMax::can(new Attribute(new Name('min'))))
        ->toBeFalse();
});
