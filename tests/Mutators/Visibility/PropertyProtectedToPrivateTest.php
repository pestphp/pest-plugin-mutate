<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Visibility\PropertyProtectedToPrivate;

it('mutates a protected property to a private property', function (): void {
    expect(mutateCode(PropertyProtectedToPrivate::class, <<<'CODE'
        <?php

        class Foo
        {
            protected bool $bar = true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        class Foo
        {
            private bool $bar = true;
        }
        CODE);
});

it('mutates a protected constructor property to a private constructor property', function (): void {
    expect(mutateCode(PropertyProtectedToPrivate::class, <<<'CODE'
        <?php

        class Foo
        {
            public function __construct(protected bool $bar = true)
            {
            }
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        class Foo
        {
            public function __construct(private bool $bar = true)
            {
            }
        }
        CODE);
});
