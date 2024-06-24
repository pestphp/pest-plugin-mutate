<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\ControlStructures\TernaryNegated;
use Pest\Mutate\Support\PhpParserFactory;

it('mutates an ternary condition to be negated', function (): void {
    expect(mutateCode(TernaryNegated::class, <<<'CODE'
        <?php

        return $a > $b ? 'a' : 'b';
        CODE))->toBe(PhpParserFactory::version() === 4 ?
        <<<'CODE'
        <?php

        return !($a > $b) ? 'a' : 'b';
        CODE :
        <<<'CODE'
        <?php

        return (!($a > $b)) ? 'a' : 'b';
        CODE);
});

it('mutates a shorthand ternary condition to be negated', function (): void {
    expect(mutateCode(TernaryNegated::class, <<<'CODE'
        <?php

        return $a ?: $b;
        CODE))->toBe(PhpParserFactory::version() === 4 ?
        <<<'CODE'
        <?php

        return !$a ? $a : $b;
        CODE :
        <<<'CODE'
        <?php

        return (!$a) ? $a : $b;
        CODE);
});
