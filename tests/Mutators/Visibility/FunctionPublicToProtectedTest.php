<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Visibility\FunctionPublicToProtected;
use Pest\Mutate\Support\PhpParserFactory;

it('mutates a public function to a protected function', function (): void {
    expect(mutateCode(FunctionPublicToProtected::class, <<<'CODE'
        <?php

        class Foo
        {
            public function bar(): bool
            {
                return true;
            }
        }
        CODE))->toBe(PhpParserFactory::version() === 4 ?
        <<<'CODE'
        <?php
        
        class Foo
        {
            protected function bar() : bool
            {
                return true;
            }
        }
        CODE :
        <<<'CODE'
        <?php
        
        class Foo
        {
            protected function bar(): bool
            {
                return true;
            }
        }
        CODE);
});
