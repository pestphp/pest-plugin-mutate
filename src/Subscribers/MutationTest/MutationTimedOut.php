<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers\MutationTest;

use Pest\Mutate\Contracts\Printer;
use Pest\Mutate\Event\Events\Test\Outcome\Timeout;
use Pest\Mutate\Event\Events\Test\Outcome\TimeoutSubscriber;
use Pest\Support\Container;
use Symfony\Component\Console\Output\OutputInterface;

class MutationTimedOut implements TimeoutSubscriber
{
    public function notify(Timeout $event): void
    {
        Container::getInstance()->get(Printer::class)->reportTimedOutMutation($event->test); // @phpstan-ignore-line

        // TODO: move to default printer
        Container::getInstance()->get(OutputInterface::class)->writeln('Mutant for '.$event->test->mutation->file->getRealPath().':'.$event->test->mutation->originalNode->getLine().' timed out. ('.$event->test->mutation->mutator.')'); // @phpstan-ignore-line
    }
}
