<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Contracts\Printer;
use Pest\Support\Container;
use PHPUnit\Event\Application\Finished;
use PHPUnit\Event\Application\FinishedSubscriber;
use PHPUnit\TestRunner\TestResult\Facade;

/**
 * @internal
 */
final class EnsureInitialTestRunWasSuccessful implements FinishedSubscriber
{
    public function notify(Finished $event): void
    {
        /** @var MutationTestRunner $mutationTestRunner */
        $mutationTestRunner = Container::getInstance()->get(MutationTestRunner::class);

        if (! $mutationTestRunner->isEnabled()) {
            return;
        }

        if (Facade::result()->wasSuccessful()) {
            return;
        }

        Container::getInstance()->get(Printer::class)->reportError('Initial test run failed, aborting mutation testing.'); // @phpstan-ignore-line

        exit(1);
    }
}
