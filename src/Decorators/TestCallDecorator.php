<?php

declare(strict_types=1);

namespace Pest\Mutate\Decorators;

use Pest\Mutate\Contracts\Configuration;
use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Support\Configuration\TestConfiguration;
use Pest\Mutate\Tester\MutationTestRunnerFake;
use Pest\PendingCalls\TestCall;
use Pest\Plugins\Only;
use Pest\Support\Container;

// @codeCoverageIgnoreStart
class TestCallDecorator implements Configuration
{
    private MutationTestRunner $testRunner;

    private TestConfiguration $configuration;

    public function __construct(private readonly TestCall $testCall)
    {
    }

    /**
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $name, array $arguments): TestCall
    {
        return $this->testCall->$name(...$arguments); // @phpstan-ignore-line
    }

    public function mutate(string $profile = 'default'): self
    {
        if (! str_starts_with($profile, ConfigurationRepository::FAKE)) {
            Only::enable($this->testCall);

            $this->testRunner = Container::getInstance() // @phpstan-ignore-line
                ->get(MutationTestRunner::class);

            Container::getInstance()->get(ConfigurationRepository::class)->setProfile($profile); // @phpstan-ignore-line

            $this->configuration = Container::getInstance()->get(ConfigurationRepository::class) // @phpstan-ignore-line
                ->testConfiguration;
        } else {
            $this->testRunner = new MutationTestRunnerFake();

            $this->configuration = Container::getInstance()->get(ConfigurationRepository::class) // @phpstan-ignore-line
                ->fakeTestConfiguration($profile);
        }

        $this->testRunner->enable(); // @phpstan-ignore-line

        $this->coveredOnly();

        return $this;
    }

    public function coveredOnly(bool $coveredOnly = true): self
    {
        $this->configuration->coveredOnly($coveredOnly);

        return $this;
    }

    public function min(float $minScore, bool $failOnZeroMutations = null): self
    {
        $this->configuration->min($minScore);
        if ($failOnZeroMutations !== null) {
            $this->configuration->ignoreMinScoreOnZeroMutations($failOnZeroMutations);
        }

        return $this;
    }

    public function ignoreMinScoreOnZeroMutations(bool $ignore = true): self
    {
        $this->configuration->ignoreMinScoreOnZeroMutations($ignore);

        return $this;
    }

    /**
     * @param  array<int, string>|string  ...$paths
     */
    public function path(array|string ...$paths): self
    {
        $this->configuration->path(...$paths);

        return $this;
    }

    /**
     * @param  array<int, string>|string  ...$paths
     */
    public function ignore(array|string ...$paths): self
    {
        $this->configuration->ignore(...$paths);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function mutator(string|array ...$mutators): self
    {
        $this->configuration->mutator(...$mutators);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function except(string|array ...$mutators): self
    {
        $this->configuration->except(...$mutators);

        return $this;
    }

    public function parallel(bool $parallel = true): self
    {
        $this->configuration->parallel($parallel);

        return $this;
    }

    public function processes(int $processes = null): Configuration
    {
        $this->configuration->processes($processes);

        return $this;
    }

    public function profile(bool $parallel = true): self
    {
        $this->configuration->profile($parallel);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function class(string|array ...$classes): self
    {
        $this->configuration->class(...$classes);

        return $this;
    }

    public function stopOnSurvived(bool $stopOnSurvived = true): self
    {
        $this->configuration->stopOnSurvived($stopOnSurvived);

        return $this;
    }

    public function stopOnNotCovered(bool $stopOnNotCovered = true): self
    {
        $this->configuration->stopOnNotCovered($stopOnNotCovered);

        return $this;
    }

    public function bail(): self
    {
        $this->configuration->bail();

        return $this;
    }

    public function uncommittedOnly(bool $uncommittedOnly = true): self
    {
        $this->configuration->uncommittedOnly($uncommittedOnly);

        return $this;
    }

    public function changedOnly(?string $branch = 'main'): Configuration
    {
        $this->configuration->changedOnly($branch);

        return $this;
    }
}
// @codeCoverageIgnoreEnd
