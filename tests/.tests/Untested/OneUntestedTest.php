<?php

declare(strict_types=1);

use Tests\Fixtures\Classes\SizeHelper;

mutates(SizeHelper::class);

it('has escaped mutants', function (int $size, bool $isBig) {
    expect(SizeHelper::isBig($size))
        ->toBe($isBig);
})->with([
    [10, false],
    [500, true],
]);
