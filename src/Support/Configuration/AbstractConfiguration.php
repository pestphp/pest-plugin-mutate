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
     * @var class-string<Mutator>[]|null
     */
    private ?array $excludedMutators = null;

    /**
     * @var string[]|null
     */
    private ?array $classes = null;

    private ?float $minMSI = null;

    private ?bool $coveredOnly = null;

    private ?bool $parallel = null;

    private ?bool $stopOnSurvived = null;

    private ?bool $stopOnNotCovered = null;

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
        $this->mutators = $this->buildMutatorsList(...$mutators);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function except(array|string ...$mutators): self
    {
        $this->excludedMutators = $this->buildMutatorsList(...$mutators);

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

    public function stopOnSurvived(bool $stopOnSurvived = true): self
    {
        $this->stopOnSurvived = $stopOnSurvived;

        return $this;
    }

    public function stopOnNotCovered(bool $stopOnNotCovered = true): self
    {
        $this->stopOnNotCovered = $stopOnNotCovered;

        return $this;
    }

    public function bail(): self
    {
        $this->stopOnSurvived = true;
        $this->stopOnNotCovered = true;

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
     * @return array{paths?: string[], mutators?: class-string<Mutator>[], excluded_mutators?: class-string<Mutator>[], classes?: string[], parallel?: bool, min_msi?: float, covered_only?: bool, stop_on_survived?: bool, stop_on_not_covered?: bool}
     */
    public function toArray(): array
    {
        return array_filter([
            'paths' => $this->paths,
            'mutators' => $this->mutators !== null ? array_values(array_diff($this->mutators, $this->excludedMutators ?? [])) : null,
            'excluded_mutators' => $this->excludedMutators,
            'classes' => $this->classes,
            'parallel' => $this->parallel,
            'min_msi' => $this->minMSI,
            'covered_only' => $this->coveredOnly,
            'stop_on_survived' => $this->stopOnSurvived,
            'stop_on_not_covered' => $this->stopOnNotCovered,
        ], fn (mixed $value): bool => ! is_null($value));
    }

    /**
     * @param  array<int, class-string<Mutator|MutatorSet>>|class-string<Mutator|MutatorSet>  ...$mutators
     * @return array<int, class-string<Mutator>>
     */
    private function buildMutatorsList(array|string ...$mutators): array
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

        return $mutators; // @phpstan-ignore-line
    }
}
