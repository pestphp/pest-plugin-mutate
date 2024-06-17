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
use Pest\Mutate\Event\Events\Test\Outcome\Escaped;
use Pest\Mutate\Event\Events\Test\Outcome\EscapedSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\Timeout;
use Pest\Mutate\Event\Events\Test\Outcome\TimeoutSubscriber;
use Pest\Mutate\Event\Events\TestSuite\FinishMutationGeneration;
use Pest\Mutate\Event\Events\TestSuite\FinishMutationGenerationSubscriber;
use Pest\Mutate\Event\Events\TestSuite\FinishMutationSuite;
use Pest\Mutate\Event\Events\TestSuite\FinishMutationSuiteSubscriber;
use Pest\Mutate\Event\Events\TestSuite\StartMutationGeneration;
use Pest\Mutate\Event\Events\TestSuite\StartMutationGenerationSubscriber;
use Pest\Mutate\Event\Events\TestSuite\StartMutationSuite;
use Pest\Mutate\Event\Events\TestSuite\StartMutationSuiteSubscriber;
use Pest\Mutate\Event\Facade;
use Pest\Mutate\Support\Printers\DefaultPrinter;
use Pest\Support\Container;
use PHPUnit\Event\Application\Started;
use PHPUnit\Event\Application\StartedSubscriber;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
class EnsurePrinterIsRegistered implements StartedSubscriber
{
    public function notify(Started $event): void
    {
        /** @var MutationTestRunner $mutationTestRunner */
        $mutationTestRunner = Container::getInstance()->get(MutationTestRunner::class);

        if (! $mutationTestRunner->isEnabled()) {
            return;
        }

        $printer = new DefaultPrinter(Container::getInstance()->get(OutputInterface::class)); // @phpstan-ignore-line

        if ($_SERVER['COLLISION_PRINTER_COMPACT'] ?? false) {
            $printer->compact();
        }

        Container::getInstance()->add(Printer::class, $printer);

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

            new class($printer) extends PrinterSubscriber implements EscapedSubscriber
            {
                public function notify(Escaped $event): void
                {
                    $this->printer()->reportEscapedMutation($event->test);
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

            // MutationSuite
            new class($printer) extends PrinterSubscriber implements StartMutationGenerationSubscriber
            {
                public function notify(StartMutationGeneration $event): void
                {
                    $this->printer()->reportMutationGenerationStarted($event->mutationSuite);
                }
            },

            new class($printer) extends PrinterSubscriber implements FinishMutationGenerationSubscriber
            {
                public function notify(FinishMutationGeneration $event): void
                {
                    $this->printer()->reportMutationGenerationFinished($event->mutationSuite);
                }
            },

            new class($printer) extends PrinterSubscriber implements StartMutationSuiteSubscriber
            {
                public function notify(StartMutationSuite $event): void
                {
                    $this->printer()->reportMutationSuiteStarted($event->mutationSuite);
                }
            },

            new class($printer) extends PrinterSubscriber implements FinishMutationSuiteSubscriber
            {
                public function notify(FinishMutationSuite $event): void
                {
                    $this->printer()->reportMutationSuiteFinished($event->mutationSuite);
                }
            },
        ];

        Facade::instance()->registerSubscribers(...$subscribers);
    }
}
