<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Support\Container;
use PHPUnit\Event\TestSuite\Loaded;
use PHPUnit\Event\TestSuite\LoadedSubscriber;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;
use function Termwind\renderUsing;

/**
 * @internal
 */
final class DisplayInitialTestRunMessage implements LoadedSubscriber
{
    /**
     * Runs the subscriber.
     */
    public function notify(Loaded $event): void
    {
        /** @var MutationTestRunner $mutationTestRunner */
        $mutationTestRunner = Container::getInstance()->get(MutationTestRunner::class);

        if (! $mutationTestRunner->isEnabled()) {
            return;
        }

        renderUsing(Container::getInstance()->get(OutputInterface::class)); // @phpstan-ignore-line
        render('<div class="mx-2 mt-1">Running initial test suite:'.($mutationTestRunner->getEnabledProfile() !== 'default' ? (' (Profile: '.$mutationTestRunner->getEnabledProfile().')') : '').'</div>');
    }
}
