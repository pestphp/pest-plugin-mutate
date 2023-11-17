<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Math\MinToMax;
use PhpParser\Node\Attribute;
use PhpParser\Node\Name;

it('does not replace expression function calls', function (): void {
    mutateCode(MinToMax::class, <<<'CODE'
        <?php

        $a = ($this->min)(1, 2);
        CODE);
})->expectExceptionMessage('No mutation performed');

it('can not mutate non function call nodes', function (): void {
    expect(MinToMax::can(new Attribute(new Name('min'))))
        ->toBeFalse();
});
