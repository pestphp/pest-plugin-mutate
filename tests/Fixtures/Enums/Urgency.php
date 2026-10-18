<?php

declare(strict_types=1);

namespace Tests\Fixtures\Enums;

enum Urgency: int
{
    case Low = 1;
    case High = 2;

    public function isHigh(): bool
    {
        return $this->value >= 2;
    }
}
