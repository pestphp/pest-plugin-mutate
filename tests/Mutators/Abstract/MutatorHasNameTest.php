<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Math\MinToMax;

it('has a name', function (): void {
    expect(MinToMax::name())->toBe('MinToMax');
});
