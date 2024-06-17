<?php

declare(strict_types=1);

namespace Pest\Mutate\Event;

use Pest\Mutate\Event\Events\Test\HookMethod\BeforeFirstTestExecuted;
use Pest\Mutate\Event\Events\Test\HookMethod\BeforeFirstTestExecutedSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\Caught;
use Pest\Mutate\Event\Events\Test\Outcome\CaughtSubscriber;
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
use Pest\Mutate\MutationSuite;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;

class Emitter
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (! isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function mutationCaught(MutationTest $test): void
    {
        $event = new Caught($test);

        foreach (Facade::instance()->subscribers()[CaughtSubscriber::class] ?? [] as $subscriber) {
            /** @var CaughtSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }

    public function mutationEscaped(MutationTest $test): void
    {
        $event = new Escaped($test);

        foreach (Facade::instance()->subscribers()[EscapedSubscriber::class] ?? [] as $subscriber) {
            /** @var EscapedSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }

    public function mutationTimedOut(MutationTest $test): void
    {
        $event = new Timeout($test);

        foreach (Facade::instance()->subscribers()[TimeoutSubscriber::class] ?? [] as $subscriber) {
            /** @var TimeoutSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }

    public function mutationNotCovered(MutationTest $test): void
    {
        $event = new NotCovered($test);

        foreach (Facade::instance()->subscribers()[NotCoveredSubscriber::class] ?? [] as $subscriber) {
            /** @var NotCoveredSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }

    public function startTestCollection(MutationTestCollection $testCollection): void
    {
        $event = new BeforeFirstTestExecuted($testCollection);

        foreach (Facade::instance()->subscribers()[BeforeFirstTestExecutedSubscriber::class] ?? [] as $subscriber) {
            /** @var BeforeFirstTestExecutedSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }

    public function startMutationGeneration(MutationSuite $mutationSuite): void
    {
        $event = new StartMutationGeneration($mutationSuite);

        foreach (Facade::instance()->subscribers()[StartMutationGenerationSubscriber::class] ?? [] as $subscriber) {
            /** @var StartMutationGenerationSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }

    public function finishMutationGeneration(MutationSuite $mutationSuite): void
    {
        $event = new FinishMutationGeneration($mutationSuite);

        foreach (Facade::instance()->subscribers()[FinishMutationGenerationSubscriber::class] ?? [] as $subscriber) {
            /** @var FinishMutationGenerationSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }

    public function startMutationSuite(MutationSuite $mutationSuite): void
    {
        $event = new StartMutationSuite($mutationSuite);

        foreach (Facade::instance()->subscribers()[StartMutationSuiteSubscriber::class] ?? [] as $subscriber) {
            /** @var StartMutationSuiteSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }

    public function finishMutationSuite(MutationSuite $mutationSuite): void
    {
        $event = new FinishMutationSuite($mutationSuite);

        foreach (Facade::instance()->subscribers()[FinishMutationSuiteSubscriber::class] ?? [] as $subscriber) {
            /** @var FinishMutationSuiteSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }
}
