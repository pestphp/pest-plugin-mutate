<?php

declare(strict_types=1);

use Tests\Fixtures\Classes\SizeHelper;

mutates(SizeHelper::class);

foreach (range(1, 400) as $case) {
    it("renews a small subscription in case {$case}", function (): void {
        expect(SizeHelper::isBig(10))->toBeFalse();
    });
}

it('checks the boundary', function (): void {
    expect(SizeHelper::isBig(99))->toBeFalse()
        ->and(SizeHelper::isBig(100))->toBeTrue()
        ->and(SizeHelper::isBig(101))->toBeTrue();
});
