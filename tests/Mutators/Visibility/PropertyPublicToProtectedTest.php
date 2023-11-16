<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Visibility\PropertyPublicToProtected;

it('mutates a public property to a protected property', function (): void {
    expect(mutateCode(PropertyPublicToProtected::class, <<<'CODE'
        <?php

        class Foo
        {
            public bool $bar = true;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        class Foo
        {
            protected bool $bar = true;
        }
        CODE);
});

it('mutates a public constructor property to a protected constructor property', function (): void {
    expect(mutateCode(PropertyPublicToProtected::class, <<<'CODE'
        <?php

        class Foo
        {
            public function __construct(public bool $bar = true)
            {
            }
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        class Foo
        {
            public function __construct(protected bool $bar = true)
            {
            }
        }
        CODE);
});
