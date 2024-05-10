<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Return\AlwaysReturnEmptyArray;

it('mutates a return statement to an empty array', function (): void {
    expect(mutateCode(AlwaysReturnEmptyArray::class, <<<'CODE'
        <?php

        function foo(): array
        {
            return [1];
        }
        function bar(): int|array
        {
            return [1];
        }
        CODE))->toBe(<<<'CODE'
        <?php

        function foo(): array
        {
            return [];
        }
        function bar(): int|array
        {
            return [];
        }
        CODE);
});

it('mutates a return statement in class functions', function (): void {
    expect(mutateCode(AlwaysReturnEmptyArray::class, <<<'CODE'
        <?php

        class Foo
        {
            function foo(): array
            {
                return [1];
            }
        }
        CODE))->toBe(<<<'CODE'
        <?php

        class Foo
        {
            function foo(): array
            {
                return [];
            }
        }
        CODE);
});

it('does not mutate if return is already an empty array', function (): void {
    mutateCode(AlwaysReturnEmptyArray::class, <<<'CODE'
        <?php

        function foo(): array
        {
            return [];
        }
        CODE);
})->expectExceptionMessage('No mutation performed');
