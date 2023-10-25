<?php

declare(strict_types=1);

namespace Pest\Mutate\Factories;

use Pest\Mutate\Profile;
use Pest\Mutate\Profiles;

class ProfileFactory
{
    private readonly Profile $profile;

    public function __construct(string $name)
    {
        $this->profile = Profiles::get($name);
    }

    public function min(float $minMSI): self
    {
        $this->profile->minMSI = $minMSI;

        return $this;
    }

    public function coveredOnly(bool $coveredOnly): self
    {
        $this->profile->coveredOnly = $coveredOnly;

        return $this;
    }
}
