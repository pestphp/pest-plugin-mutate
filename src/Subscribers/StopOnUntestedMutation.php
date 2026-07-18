<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Event\Events\Test\Outcome\Untested;
use Pest\Mutate\Event\Events\Test\Outcome\UntestedSubscriber;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Support\Container;

/**
 * @internal
 */
final class StopOnUntestedMutation implements UntestedSubscriber
{
    public function notify(Untested $event): void
    {
        /** @var ConfigurationRepository $configurationRepository */
        $configurationRepository = Container::getInstance()->get(ConfigurationRepository::class);

        if (! $configurationRepository->mergedConfiguration()->stopOnUntested) {
            return;
        }

        /** @var \Pest\Mutate\Tester\MutationTestRunner $mutationTestRunner */
        $mutationTestRunner = Container::getInstance()->get(MutationTestRunner::class);

        $mutationTestRunner->stopExecution();
    }
}
