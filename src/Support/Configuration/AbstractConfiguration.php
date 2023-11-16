<?php

declare(strict_types=1);

namespace Pest\Mutate\Support\Configuration;

use Pest\Mutate\Contracts\Configuration as ConfigurationContract;
use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Exceptions\InvalidMutatorException;

abstract class AbstractConfiguration implements ConfigurationContract
{
    /**
     * @var string[]|null
     */
    private ?array $paths = null;

    /**
     * @var class-string<Mutator>[]|null
     */
    private ?array $mutators = null;

    /**
     * @var string[]|null
     */
    private ?array $classes = null;

    private ?float $minMSI = null;

    private ?bool $coveredOnly = null;

    private ?bool $parallel = null;

    private ?bool $stopOnSurvival = null;

    private ?bool $stopOnUncovered = null;

    /**
     * {@inheritDoc}
     */
    public function path(array|string ...$paths): self
    {
        $this->paths = array_merge(...array_map(fn (string|array $path): array => is_string($path) ? [$path] : $path, $paths));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function mutator(array|string ...$mutators): self
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

        $this->mutators = $mutators; // @phpstan-ignore-line

        return $this;
    }

    public function min(float $minMSI): self
    {
        $this->minMSI = $minMSI;

        return $this;
    }

    public function coveredOnly(bool $coveredOnly = true): self
    {
        $this->coveredOnly = $coveredOnly;

        return $this;
    }

    public function parallel(bool $parallel = true): self
    {
        $this->parallel = $parallel;

        return $this;
    }

    public function stopOnSurvival(bool $stopOnSurvival = true): self
    {
        $this->stopOnSurvival = $stopOnSurvival;

        return $this;
    }

    public function stopOnUncovered(bool $stopOnUncovered = true): self
    {
        $this->stopOnUncovered = $stopOnUncovered;

        return $this;
    }

    public function bail(): self
    {
        $this->stopOnSurvival = true;
        $this->stopOnUncovered = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function class(string|array ...$classes): self
    {
        $this->classes = array_merge(...array_map(fn (string|array $class): array => is_string($class) ? [$class] : $class, $classes));

        return $this;
    }

    /**
     * @return array{paths?: string[], mutators?: class-string<Mutator>[], classes?: string[], parallel?: bool, min_msi?: float, covered_only?: bool, stop_on_survival?: bool, stop_on_uncovered?: bool}
     */
    public function toArray(): array
    {
        return array_filter([
            'paths' => $this->paths,
            'mutators' => $this->mutators,
            'classes' => $this->classes,
            'parallel' => $this->parallel,
            'min_msi' => $this->minMSI,
            'covered_only' => $this->coveredOnly,
            'stop_on_survival' => $this->stopOnSurvival,
            'stop_on_uncovered' => $this->stopOnUncovered,
        ], fn (mixed $value): bool => ! is_null($value));
    }
}
