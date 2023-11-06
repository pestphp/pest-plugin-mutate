<?php

declare(strict_types=1);

namespace Pest\Mutate\Event;

use Pest\Mutate\Event\Events\Test\Outcome\Killed;
use Pest\Mutate\Event\Events\Test\Outcome\KilledSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\NotCovered;
use Pest\Mutate\Event\Events\Test\Outcome\NotCoveredSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\Survived;
use Pest\Mutate\Event\Events\Test\Outcome\SurvivedSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\Timeout;
use Pest\Mutate\Event\Events\Test\Outcome\TimeoutSubscriber;
use Pest\Mutate\MutationTest;

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

    public function mutationKilled(MutationTest $test): void
    {
        $event = new Killed($test);

        foreach (Facade::instance()->subscribers()[KilledSubscriber::class] ?? [] as $subscriber) {
            /** @var KilledSubscriber $subscriber */
            $subscriber->notify($event);
        }
    }

    public function mutationSurvived(MutationTest $test): void
    {
        $event = new Survived($test);

        foreach (Facade::instance()->subscribers()[SurvivedSubscriber::class] ?? [] as $subscriber) {
            /** @var SurvivedSubscriber $subscriber */
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
}
