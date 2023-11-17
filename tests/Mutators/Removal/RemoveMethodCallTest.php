<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Removal\RemoveMethodCall;

it('removes a method call', function (): void {
    expect(mutateCode(RemoveMethodCall::class, <<<'CODE'
        <?php

        class Foo
        {
            function foo()
            {
                $this->bar();
            }
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        class Foo
        {
            function foo()
            {
                
            }
        }
        CODE);
});

it('removes a static method call', function (): void {
    expect(mutateCode(RemoveMethodCall::class, <<<'CODE'
        <?php

        class Foo
        {
            function foo()
            {
                self::bar();
            }
        }
        CODE))->toBe(<<<'CODE'
        <?php
        
        class Foo
        {
            function foo()
            {
                
            }
        }
        CODE);
});
