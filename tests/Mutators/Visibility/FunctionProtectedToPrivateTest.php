<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Visibility\FunctionProtectedToPrivate;
use Pest\Mutate\Support\PhpParserFactory;

it('mutates a protected function to a private function', function (): void {
    expect(mutateCode(FunctionProtectedToPrivate::class, <<<'CODE'
        <?php

        class Foo
        {
            protected function bar(): bool
            {
                return true;
            }
        }
        CODE))->toBe(PhpParserFactory::version() === 4 ?
        <<<'CODE'
        <?php
        
        class Foo
        {
            private function bar() : bool
            {
                return true;
            }
        }
        CODE :
        <<<'CODE'
        <?php
        
        class Foo
        {
            private function bar(): bool
            {
                return true;
            }
        }
        CODE);
});
