<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Return\AlwaysReturnNull;
use Pest\Mutate\Support\PhpParserFactory;

it('mutates a return statement to null if it is not null', function (): void {
    expect(mutateCode(AlwaysReturnNull::class, <<<'CODE'
        <?php

        function foo()
        {
            return 1;
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        function foo()
        {
            return null;
        }
        CODE);
});

it('mutates if return type is possibly null', function (): void {
    expect(mutateCode(AlwaysReturnNull::class, <<<'CODE'
        <?php

        function foo(): ?int
        {
            return 1;
        }
        function bar(): int|null
        {
            return 1;
        }
        CODE))->toBe(PhpParserFactory::version() === 4 ?
        <<<'CODE'
        <?php
        
        function foo() : ?int
        {
            return null;
        }
        function bar() : int|null
        {
            return null;
        }
        CODE :
        <<<'CODE'
        <?php
        
        function foo(): ?int
        {
            return null;
        }
        function bar(): int|null
        {
            return null;
        }
        CODE);
});

it('mutates a return statement in class functions', function (): void {
    expect(mutateCode(AlwaysReturnNull::class, <<<'CODE'
        <?php

        class Foo
        {
            function foo(): ?int
            {
                return 1;
            }
        }
        CODE))->toBe(PhpParserFactory::version() === 4 ?
        <<<'CODE'
        <?php
        
        class Foo
        {
            function foo() : ?int
            {
                return null;
            }
        }
        CODE :
        <<<'CODE'
        <?php
        
        class Foo
        {
            function foo(): ?int
            {
                return null;
            }
        }
        CODE);
});

it('does not mutate if it already returns null', function (): void {
    mutateCode(AlwaysReturnNull::class, <<<'CODE'
        <?php

        function foo()
        {
            return null;
        }
        CODE);
})->expectExceptionMessage('No mutation performed');

it('does not mutate if null is not a valid return type', function (): void {
    mutateCode(AlwaysReturnNull::class, <<<'CODE'
        <?php

        function foo(): int
        {
            return 1;
        }
        CODE);
})->expectExceptionMessage('No mutation performed');
