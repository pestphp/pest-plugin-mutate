<?php

declare(strict_types=1);

namespace Pest\Mutate\Decorators;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Factories\ProfileFactory;
use Pest\Mutate\Profile;
use Pest\Mutate\Tester\MutationTestRunnerFake;
use Pest\PendingCalls\TestCall;
use Pest\Plugins\Only;
use Pest\Support\Container;

// @codeCoverageIgnoreStart
class TestCallDecorator implements \Pest\Mutate\Contracts\ProfileFactory
{
    private MutationTestRunner $testRunner;

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
        if (! str_starts_with($profile, Profile::FAKE)) {
            Only::enable($this->testCall);

            $this->testRunner = Container::getInstance() // @phpstan-ignore-line
                ->get(MutationTestRunner::class);
        } else {
            $this->testRunner = new MutationTestRunnerFake();
        }

        $this->testRunner->enable($profile); // @phpstan-ignore-line

        $this->coveredOnly();

        return $this;
    }

    public function coveredOnly(bool $coveredOnly = true): self
    {
        $this->_profileFactory()->coveredOnly($coveredOnly);

        return $this;
    }

    public function min(float $minMSI): self
    {
        $this->_profileFactory()->min($minMSI);

        return $this;
    }

    /**
     * @param  array<int, string>|string  ...$paths
     */
    public function paths(array|string ...$paths): self
    {
        $this->_profileFactory()->paths(...$paths);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function mutators(string|array ...$mutators): self
    {
        $this->_profileFactory()->mutators(...$mutators);

        return $this;
    }

    public function parallel(bool $parallel = true): self
    {
        $this->_profileFactory()->parallel($parallel);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function class(string|array ...$classes): self
    {
        $this->_profileFactory()->class(...$classes);

        return $this;
    }

    private function _profileFactory(): ProfileFactory
    {
        return $this->testRunner->getProfileFactory();
    }
}
// @codeCoverageIgnoreEnd
