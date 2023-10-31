<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Logical\InstanceOfToTrue;

it('mutates a instance of check to true', function (): void {
    expect(mutateCode(InstanceOfToTrue::class, <<<'CODE'
        <?php

        return $a instanceof A;
        CODE))->toBe(<<<'CODE'
        <?php
        
        return true;
        CODE);
});
