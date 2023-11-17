<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Equality\SpaceshipSwitchSides;

it('mutates a spaceship comparison by switching the parameters', function (): void {
    expect(mutateCode(SpaceshipSwitchSides::class, <<<'CODE'
        <?php

        return $a <=> $b;
        CODE))->toBe(<<<'CODE'
        <?php
        
        return $b <=> $a;
        CODE);
});

it('does not mutate other operators', function (): void {
    mutateCode(SpaceshipSwitchSides::class, <<<'CODE'
        <?php

        return $a + $b;
        CODE);
})->expectExceptionMessage('No mutation performed');
