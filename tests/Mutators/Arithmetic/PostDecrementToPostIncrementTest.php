<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\Arithmetic\PostDecrementToPostIncrement;

it('mutates a post decrement operation to post increment', function (): void {
    expect(mutateCode(PostDecrementToPostIncrement::class, <<<'CODE'
        <?php

        return $a--;
        CODE))->toBe(<<<'CODE'
        <?php

        return $a++;
        CODE);
});
