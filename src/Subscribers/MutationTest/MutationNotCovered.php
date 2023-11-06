<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers\MutationTest;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\Event\Events\Test\Outcome\NotCovered;
use Pest\Mutate\Event\Events\Test\Outcome\NotCoveredSubscriber;
use Pest\Support\Container;

class MutationNotCovered implements NotCoveredSubscriber
{
    public function notify(NotCovered $event): void
    {
        Container::getInstance()->get(Printer::class)->reportNotCoveredMutation($event->test); // @phpstan-ignore-line
    }
}
