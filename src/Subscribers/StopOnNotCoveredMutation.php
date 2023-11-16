<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Event\Events\Test\Outcome\NotCovered;
use Pest\Mutate\Event\Events\Test\Outcome\NotCoveredSubscriber;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Support\Container;

/**
 * @internal
 */
final class StopOnNotCoveredMutation implements NotCoveredSubscriber
{
    public function notify(NotCovered $event): void
    {
        if (! Container::getInstance()->get(ConfigurationRepository::class) // @phpstan-ignore-line
            ->mergedConfiguration()
            ->stopOnNotCovered) {
            return;
        }

        Container::getInstance()->get(MutationTestRunner::class) // @phpstan-ignore-line
            ->stopExecution();
    }
}
