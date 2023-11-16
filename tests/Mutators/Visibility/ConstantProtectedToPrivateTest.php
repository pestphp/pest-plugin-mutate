<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Visibility\ConstantProtectedToPrivate;

it('mutates a protected constant to a private constant', function (): void {
    expect(mutateCode(ConstantProtectedToPrivate::class, <<<'CODE'
        <?php

        class Foo
        {
            protected const BAR = true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        class Foo
        {
            private const BAR = true;
        }
        CODE);
});
