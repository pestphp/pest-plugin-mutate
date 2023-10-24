<?php

declare(strict_types=1);

namespace Pest\Mutate;

class Config
{
    public function __construct(
        public float $minMSI = 0,
        public bool $coveredOnly = false,
    ) {
        //
    }
}
