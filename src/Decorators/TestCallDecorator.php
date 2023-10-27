<?php

declare(strict_types=1);

namespace Pest\Mutate\Decorators;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Factories\ProfileFactory;
use Pest\Mutate\Profile;
use Pest\PendingCalls\TestCall;
use Pest\Plugins\Only;
use Pest\Support\Container;

// @codeCoverageIgnoreStart
class TestCallDecorator implements \Pest\Mutate\Contracts\ProfileFactory
{
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
        if ($profile !== Profile::FAKE) {
            Only::enable($this->testCall);
        }

        Container::getInstance() // @phpstan-ignore-line
            ->get(MutationTestRunner::class)
            ->enable($profile);

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

    private function _profileFactory(): ProfileFactory
    {
        return Container::getInstance() // @phpstan-ignore-line
            ->get(MutationTestRunner::class)
            ->getProfileFactory();
    }
}
// @codeCoverageIgnoreEnd
