<?php

declare(strict_types=1);

namespace Pest\Mutate;

class Profile
{
    /**
     * @var array<int, string>
     */
    public array $paths = [];

    public float $minMSI = 0;

    public bool $coveredOnly = false;
}
