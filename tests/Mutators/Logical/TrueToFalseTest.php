<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\Logical\TrueToFalse;

it('mutates a true to a false', function (): void {
    expect(mutateCode(TrueToFalse::class, <<<'CODE'
        <?php

        return true;
        CODE))->toBe(<<<'CODE'
        <?php
        
        return false;
        CODE);
});

it('mutates a true to a false in a function call', function (): void {
    expect(mutateCode(TrueToFalse::class, <<<'CODE'
        <?php

        return foo(true);
        CODE))->toBe(<<<'CODE'
        <?php
        
        return foo(false);
        CODE);
});

it('does not mutate anything else', function (): void {
    expect(mutateCode(TrueToFalse::class, <<<'CODE'
        <?php

        return foo(bar);
        CODE))->toBe(<<<'CODE'
        <?php
        
        return foo(bar);
        CODE);
});

it('does not mutate if true is the third parameter of in_array', function (): void {
    expect(mutateCode(TrueToFalse::class, <<<'CODE'
        <?php

        return in_array(1, [1, 2, 3], true);
        CODE))->toBe(<<<'CODE'
        <?php
        
        return in_array(1, [1, 2, 3], true);
        CODE);
});

it('does not mutate if true is the third parameter of array_search', function (): void {
    expect(mutateCode(TrueToFalse::class, <<<'CODE'
        <?php

        return array_search(1, [1, 2, 3], true);
        CODE))->toBe(<<<'CODE'
        <?php
        
        return array_search(1, [1, 2, 3], true);
        CODE);
});

it('mutates on expression function calls', function (): void {
    expect(mutateCode(TrueToFalse::class, <<<'CODE'
        <?php

        ($this->min)(true);
        CODE))->toBe(<<<'CODE'
        <?php
        
        ($this->min)(false);
        CODE);
});
