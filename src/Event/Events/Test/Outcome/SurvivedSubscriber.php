<?php declare(strict_types=1);

namespace Pest\Mutate\Event\Events\Test\Outcome;

use Pest\Mutate\Contracts\Subscriber;

interface SurvivedSubscriber extends Subscriber
{
    public function notify(Survived $event): void;
}
