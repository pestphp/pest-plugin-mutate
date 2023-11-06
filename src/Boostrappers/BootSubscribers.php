<?php

declare(strict_types=1);

namespace Pest\Mutate\Boostrappers;

use Pest\Contracts\Bootstrapper;
use Pest\Mutate\Contracts\Subscriber;
use Pest\Mutate\Event\Facade;
use Pest\Support\Container;

/**
 * @internal
 */
final class BootSubscribers implements Bootstrapper
{
    // TODO: we will use this later to register the subscribers to stop the execution on first not killed mutation

    /**
     * The list of Subscribers.
     *
     * @var array<int, class-string<Subscriber>>
     */
    private const SUBSCRIBERS = [
    ];

    /**
     * Creates a new instance of the Boot Subscribers.
     */
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /**
     * Boots the list of Subscribers.
     */
    public function boot(): void
    {
        foreach (self::SUBSCRIBERS as $subscriber) { // @phpstan-ignore-line
            $instance = $this->container->get($subscriber);

            assert($instance instanceof Subscriber);

            Facade::instance()->registerSubscriber($instance);
        }
    }
}
