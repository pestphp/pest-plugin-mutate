<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\PostIncrementToPostDecrement;

it('mutates a post increment operation to post decrement', function (): void {
    expect(mutateCode(PostIncrementToPostDecrement::class, <<<'CODE'
        <?php

        return $a++;
        CODE))->toBe(<<<'CODE'
        <?php

        return $a--;
        CODE);
});
