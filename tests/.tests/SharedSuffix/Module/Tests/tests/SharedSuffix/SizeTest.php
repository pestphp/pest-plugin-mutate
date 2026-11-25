<?php

declare(strict_types=1);

use Tests\Fixtures\Classes\SizeHelper;

mutates(SizeHelper::class);

it('checks size', function (): void {
    expect(SizeHelper::isBig(99))->toBeFalse()
        ->and(SizeHelper::isBig(100))->toBeTrue()
        ->and(SizeHelper::isBig(101))->toBeTrue();
});
