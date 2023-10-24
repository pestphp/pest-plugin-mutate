<?php

declare(strict_types=1);

it('kills all the mutants', function (int $age, bool $isAdult) {
    expect(\Tests\Fixtures\Classes\AgeHelper::isAdult($age))
        ->toBe($isAdult);
})->with([
    [10, false],
    [17, false],
    [18, true],
    [25, true],
])->group('success');
