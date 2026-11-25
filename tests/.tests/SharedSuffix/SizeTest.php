<?php

declare(strict_types=1);

use Tests\Fixtures\Classes\SizeHelper;

mutates(SizeHelper::class);

it('checks size', function (): void {
    expect(SizeHelper::isBig(10))->toBeFalse();
});
