<?php

namespace Pest\Mutate\Subscribers\MutationTest;

use Pest\Mutate\Event\Events\Test\Outcome\Killed;
use Pest\Mutate\Event\Events\Test\Outcome\KilledSubscriber;
use Pest\Support\Container;
use Symfony\Component\Console\Output\OutputInterface;

class MutationKilled implements KilledSubscriber
{
    public function notify(Killed $event): void
    {
        Container::getInstance()->get(OutputInterface::class)->write('<fg=gray;options=bold>.</>');
    }
}
