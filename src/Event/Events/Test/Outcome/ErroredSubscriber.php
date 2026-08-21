<?php

declare(strict_types=1);

namespace Pest\Mutate\Event\Events\Test\Outcome;

use Pest\Mutate\Contracts\Subscriber;

interface ErroredSubscriber extends Subscriber
{
    public function notify(Errored $event): void;
}
