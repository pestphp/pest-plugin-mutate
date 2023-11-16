<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Event\Events\Test\Outcome\Survived;
use Pest\Mutate\Event\Events\Test\Outcome\SurvivedSubscriber;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Support\Container;

/**
 * @internal
 */
final class StopOnSurvivedMutation implements SurvivedSubscriber
{
    public function notify(Survived $event): void
    {
        if (! Container::getInstance()->get(ConfigurationRepository::class) // @phpstan-ignore-line
            ->mergedConfiguration()
            ->stopOnSurvived) {
            return;
        }

        Container::getInstance()->get(MutationTestRunner::class) // @phpstan-ignore-line
            ->stopExecution();
    }
}
