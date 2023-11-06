<?php

namespace Pest\Mutate\Subscribers\MutationTest;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\Event\Events\Test\Outcome\Killed;
use Pest\Mutate\Event\Events\Test\Outcome\KilledSubscriber;
use Pest\Mutate\Event\Events\Test\Outcome\NotCovered;
use Pest\Mutate\Event\Events\Test\Outcome\NotCoveredSubscriber;
use Pest\Support\Container;
use Symfony\Component\Console\Output\OutputInterface;

class MutationNotCovered implements NotCoveredSubscriber
{
    public function notify(NotCovered $event): void
    {
        Container::getInstance()->get(Printer::class)->reportNotCoveredMutation($event->test);
        Container::getInstance()->get(OutputInterface::class)->writeln('No tests found for mutation: '.$this->mutation->file->getRealPath().':'.$this->mutation->originalNode->getLine().' ('.$this->mutation->mutator::name().')');

    }
}
