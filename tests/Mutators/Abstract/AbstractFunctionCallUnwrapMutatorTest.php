<?php

declare(strict_types=1);
use Pest\Mutate\Mutators\String\UnwrapChop;
use PhpParser\Node\Attribute;
use PhpParser\Node\Name;

it('does not unwrap expression function calls', function (): void {
    mutateCode(UnwrapChop::class, <<<'CODE'
        <?php

        $a = ($this->chop)('foo');
        CODE);
})->expectExceptionMessage('No mutation performed');

it('can not mutate non function call nodes', function (): void {
    expect(UnwrapChop::can(new Attribute(new Name('chop'))))
        ->toBeFalse();
});
