<?php

declare(strict_types=1);

namespace Pest\Mutate\Event\Events\Test\Outcome;

use Pest\Mutate\Contracts\Subscriber;

interface CaughtSubscriber extends Subscriber
{
    public function notify(Caught $event): void;
}
