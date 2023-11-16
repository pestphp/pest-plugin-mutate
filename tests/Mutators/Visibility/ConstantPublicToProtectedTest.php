<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Visibility\ConstantPublicToProtected;

it('mutates a public constant to a protected constant', function (): void {
    expect(mutateCode(ConstantPublicToProtected::class, <<<'CODE'
        <?php

        class Foo
        {
            public const BAR = true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        class Foo
        {
            protected const BAR = true;
        }
        CODE);
});
