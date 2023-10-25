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

    /**
     * @param  array<int, string>|string  ...$paths
     */
    public function paths(array|string ...$paths): self
    {
        $this->profile->paths = array_merge(...array_map(fn (string|array $path): array => is_string($path) ? [$path] : $path, $paths));

        return $this;
    }

    public function min(float $minMSI): self
    {
        $this->profile->minMSI = $minMSI;

        return $this;
    }

    public function coveredOnly(bool $coveredOnly = true): self
    {
        $this->profile->coveredOnly = $coveredOnly;

        return $this;
    }
}
