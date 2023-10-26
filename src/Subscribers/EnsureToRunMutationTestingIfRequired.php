<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Support\Container;
use PHPUnit\Event\TestRunner\Started;
use PHPUnit\Event\TestRunner\StartedSubscriber;

/**
 * @internal
 */
final class EnsureToRunMutationTestingIfRequired implements StartedSubscriber
{
    /**
     * Runs the subscriber.
     */
    public function notify(Started $event): void
    {
        /** @var MutationTestRunner $mutationTestRunner */
        $mutationTestRunner = Container::getInstance()->get(MutationTestRunner::class);

        if($mutationTestRunner->isEnabled()){
            $mutationTestRunner->run();
        }
    }
}
