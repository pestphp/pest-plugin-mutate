<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Removal\RemoveEarlyReturn;

//mutate()
//    ->class(RemoveEarlyReturn::class);

it('removes an early return statement', function (): void {
    expect(mutateCode(RemoveEarlyReturn::class, <<<'CODE'
        <?php

        class Foo
        {
            function foo()
            {
                if ($a > $b) {
                    return true;
                }
                return false;
            }
        }
        CODE
    ))->toBe(<<<'CODE'
        <?php
        
        class Foo
        {
            function foo()
            {
                if ($a > $b) {
                    
                }
                return false;
            }
        }
        CODE
    );
});

it('does not remove a single return', function (): void {
    mutateCode(RemoveEarlyReturn::class, <<<'CODE'
        <?php

        class Foo
        {
            function foo()
            {
                return true;
            }
        }
        CODE
    );
})->expectExceptionMessage('No mutation performed');
