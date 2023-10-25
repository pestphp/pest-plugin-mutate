<?php

declare(strict_types=1);

it('has survival mutants', function (int $age, bool $isAdult) {
    expect(\Tests\Fixtures\Classes\AgeHelper::isAdult($age))
        ->toBe($isAdult);
})->with([
    [10, false],
    [17, false],
    [25, true],
]);
