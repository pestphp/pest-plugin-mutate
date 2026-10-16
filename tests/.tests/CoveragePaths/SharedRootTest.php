<?php

declare(strict_types=1);

use Tests\Fixtures\Classes\AgeHelper;
use Tests\Fixtures\CoveragePaths\Generated\Threshold;

require_once getenv('PEST_MUTATE_GENERATED_COVERAGE_PATHS').DIRECTORY_SEPARATOR.'Threshold.php';

it('checks a class outside the repository', function (): void {
    expect(Threshold::reached(10))->toBeTrue()
        ->and(Threshold::reached(9))->toBeFalse();
});

it('checks a class inside the repository', function (): void {
    expect(AgeHelper::isSenior(65))->toBeTrue();
});
