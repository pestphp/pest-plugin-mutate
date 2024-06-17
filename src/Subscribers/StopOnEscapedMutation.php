<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Event\Events\Test\Outcome\Escaped;
use Pest\Mutate\Event\Events\Test\Outcome\EscapedSubscriber;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Support\Container;

/**
 * @internal
 */
final class StopOnEscapedMutation implements EscapedSubscriber
{
    public function notify(Escaped $event): void
    {
        if (! Container::getInstance()->get(ConfigurationRepository::class) // @phpstan-ignore-line
            ->mergedConfiguration()
            ->stopOnEscaped) {
            return;
        }

        Container::getInstance()->get(MutationTestRunner::class) // @phpstan-ignore-line
            ->stopExecution();
    }
}
