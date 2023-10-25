<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Plugins\Mutate;
use Pest\PendingCalls\TestCall;
use Pest\Plugins\Only;

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

        Mutate::enable();

        return $this;
    }
}
