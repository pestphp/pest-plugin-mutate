<?php

namespace Pest\Mutate\Subscribers\MutationTest;

use Pest\Mutate\Event\Events\Test\Outcome\Killed;
use Pest\Mutate\Event\Events\Test\Outcome\KilledSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\Timeout;
use Pest\Mutate\Event\Events\Test\Outcome\TimeoutSubscriber;
use Pest\Support\Container;
use Symfony\Component\Console\Output\OutputInterface;

class MutationTimedOut implements TimeoutSubscriber
{
    public function notify(Timeout $event): void
    {
        Container::getInstance()->get(OutputInterface::class)->write('<fg=yellow;options=bold>t</>');
        Container::getInstance()->get(OutputInterface::class)->writeln('Mutant for '.$this->mutation->file->getRealPath().':'.$this->mutation->originalNode->getLine().' timed out. ('.$this->mutation->mutator.')');
    }
}
