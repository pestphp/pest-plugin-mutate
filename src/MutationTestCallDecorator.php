<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Factories\ProfileFactory;
use Pest\PendingCalls\TestCall;
use Pest\Plugins\Only;
use Pest\Support\Container;

class MutationTestCallDecorator
{
    public function __construct(private readonly TestCall $testCall)
    {
    }

    /**
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $name, array $arguments): TestCall
    {
        return $this->testCall->__call($name, $arguments);
    }

    public function mutate(string $profile = 'default'): self
    {
        Only::enable($this->testCall);

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

    private function _profileFactory(): ProfileFactory
    {
        return Container::getInstance() // @phpstan-ignore-line
            ->get(MutationTestRunner::class)
            ->getProfileFactory();
    }
}
