<?php

declare(strict_types=1);

use Tests\Fixtures\Classes\SizeHelper;

mutates(SizeHelper::class);

it('catches all the mutants', function (int $size, bool $isBig) {
    expect(SizeHelper::isBig($size))
        ->toBe($isBig);
})->with([
    [99, false],
    [100, true],
    [101, true],
]);
