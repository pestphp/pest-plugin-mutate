<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers\MutationTest;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\Event\Events\Test\Outcome\Killed;
use Pest\Mutate\Event\Events\Test\Outcome\KilledSubscriber;
use Pest\Support\Container;

class MutationKilled implements KilledSubscriber
{
    public function notify(Killed $event): void
    {
        Container::getInstance()->get(Printer::class)->reportKilledMutation($event->test); // @phpstan-ignore-line
    }
}
