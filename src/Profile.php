<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutators\Sets\DefaultSet;

class Profile
{
    /**
     * @var array<int, string>
     */
    public array $paths = [];

    /**
     * @var array<int, class-string<Mutator>>
     */
    public array $mutators;

    public float $minMSI = 0;

    public bool $coveredOnly = false;

    public bool $parallel = false;

    public function __construct()
    {
        $this->mutators = DefaultSet::mutators();
    }
}
