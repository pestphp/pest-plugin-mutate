<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\Event\Events\Test\HookMethod\BeforeFirstTestExecuted;
use Pest\Mutate\Event\Events\Test\HookMethod\BeforeFirstTestExecutedSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\Killed;
use Pest\Mutate\Event\Events\Test\Outcome\KilledSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\NotCovered;
use Pest\Mutate\Event\Events\Test\Outcome\NotCoveredSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\Survived;
use Pest\Mutate\Event\Events\Test\Outcome\SurvivedSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\Timeout;
use Pest\Mutate\Event\Events\Test\Outcome\TimeoutSubscriber;
use Pest\Mutate\Event\Facade;
use Pest\Support\Container;
use PHPUnit\Event\Application\Started;
use PHPUnit\Event\Application\StartedSubscriber;

/**
 * @internal
 */
final class EnsurePrinterIsRegisteredSubscriber implements StartedSubscriber
{
    public function notify(Started $event): void
    {
        /** @var MutationTestRunner $mutationTestRunner */
        $mutationTestRunner = Container::getInstance()->get(MutationTestRunner::class);

        if (! $mutationTestRunner->isEnabled()) {
            return;
        }

        /** @var Printer $printer */
        $printer = Container::getInstance()->get(Printer::class);

        $subscribers = [
            // Test > Hook Methods
            new class($printer) extends PrinterSubscriber implements BeforeFirstTestExecutedSubscriber
            {
                public function notify(BeforeFirstTestExecuted $event): void
                {
                    $this->printer()->printFilename($event->testCollection);
                }
            },

            // Test > Outcome
            new class($printer) extends PrinterSubscriber implements KilledSubscriber
            {
                public function notify(Killed $event): void
                {
                    $this->printer()->reportKilledMutation($event->test);
                }
            },

            new class($printer) extends PrinterSubscriber implements SurvivedSubscriber
            {
                public function notify(Survived $event): void
                {
                    $this->printer()->reportSurvivedMutation($event->test);
                }
            },

            new class($printer) extends PrinterSubscriber implements TimeoutSubscriber
            {
                public function notify(Timeout $event): void
                {
                    $this->printer()->reportTimedOutMutation($event->test);
                }
            },

            new class($printer) extends PrinterSubscriber implements NotCoveredSubscriber
            {
                public function notify(NotCovered $event): void
                {
                    $this->printer()->reportNotCoveredMutation($event->test);
                }
            },
        ];

        Facade::instance()->registerSubscribers(...$subscribers);
    }
}
