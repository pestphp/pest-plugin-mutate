<?php

declare(strict_types=1);

use Tests\Fixtures\Classes\SizeHelper;

mutates(SizeHelper::class);

foreach (range(1, 1200) as $case) {
    it("renews a small subscription in case {$case}", function (): void {
        expect(SizeHelper::isBig(10))->toBeFalse();
    });
}
