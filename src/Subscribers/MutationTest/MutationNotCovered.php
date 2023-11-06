<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers\MutationTest;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\Event\Events\Test\Outcome\NotCovered;
use Pest\Mutate\Event\Events\Test\Outcome\NotCoveredSubscriber;
use Pest\Support\Container;
use Symfony\Component\Console\Output\OutputInterface;

class MutationNotCovered implements NotCoveredSubscriber
{
    public function notify(NotCovered $event): void
    {
        Container::getInstance()->get(Printer::class)->reportNotCoveredMutation($event->test); // @phpstan-ignore-line

        // TODO: move to default printer
        Container::getInstance()->get(OutputInterface::class)->writeln('No tests found for mutation: '.$event->test->mutation->file->getRealPath().':'.$event->test->mutation->originalNode->getLine().' ('.$event->test->mutation->mutator::name().')'); // @phpstan-ignore-line

    }
}
