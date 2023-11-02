<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Number\DecrementFloat;
use PhpParser\Node\Scalar\LNumber;

it('decrements all floats by one', function (): void {
    expect(mutateCode(DecrementFloat::class, <<<'CODE'
        <?php

        $a = 1.0;
        CODE))->toBe(<<<'CODE'
        <?php

        $a = 0.0;
        CODE);
});

it('can not mutate non dnumber nodes', function (): void {
    expect(DecrementFloat::can(new LNumber(1)))
        ->toBeFalse();
});
