<?php

declare(strict_types=1);

namespace Pest\Mutate\Factories;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Contracts\ProfileFactory as ProfileFactoryContract;
use Pest\Mutate\Exceptions\InvalidMutatorException;
use Pest\Mutate\Profile;
use Pest\Mutate\Profiles;

class ProfileFactory implements ProfileFactoryContract
{
    private readonly Profile $profile;

    public function __construct(string $name)
    {
        $this->profile = Profiles::get($name);
    }

    /**
     * {@inheritDoc}
     */
    public function paths(array|string ...$paths): self
    {
        $this->profile->paths = array_merge(...array_map(fn (string|array $path): array => is_string($path) ? [$path] : $path, $paths));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function mutators(array|string ...$mutators): self
    {
        $mutators = array_map(fn (string|array $mutator): array => is_string($mutator) ? [$mutator] : $mutator, $mutators);

        $mutators = array_merge(...$mutators);

        $mutators = array_map(
            function (string $mutator): string {
                $constant = strtoupper((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $mutator));

                return (string) (defined('Pest\\Mutate\\Mutators::'.$constant) ? constant('Pest\\Mutate\\Mutators::'.$constant) : $mutator); // @phpstan-ignore-line
            },
            $mutators
        );

        $mutators = array_merge(...array_map(
            fn (string $mutator): array => is_a($mutator, MutatorSet::class, true) ? $mutator::mutators() : [$mutator],
            $mutators
        ));

        foreach ($mutators as $mutator) {
            if (! is_a($mutator, Mutator::class, true)) {
                throw new InvalidMutatorException("{$mutator} is not a valid mutator");
            }
        }

        $this->profile->mutators = $mutators; // @phpstan-ignore-line

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

    public function parallel(bool $parallel = true): self
    {
        $this->profile->parallel = $parallel;

        return $this;
    }
}
