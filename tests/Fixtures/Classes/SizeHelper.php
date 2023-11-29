<?php

declare(strict_types=1);

namespace Tests\Fixtures\Classes;

class SizeHelper
{
    public static function isBig(int $size): bool
    {
        return $size >= 100;
    }
}
