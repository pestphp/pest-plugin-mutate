<?php

declare(strict_types=1);

namespace Pest\Mutate\Boostrappers;

use Pest\Contracts\Bootstrapper;
use Pest\Mutate\Subscribers\DisablePhpCodeCoverageIfNotRequired;
use Pest\Mutate\Subscribers\EnsureToRunMutationTestingIfRequired;
use Pest\Mutate\Subscribers\PrepareForInitialTestRun;
use Pest\Subscribers;
use Pest\Support\Container;
use PHPUnit\Event;
use PHPUnit\Event\Subscriber;

/**
 * @internal
 */
final class BootSubscribers implements Bootstrapper
{
    /**
     * The list of Subscribers.
     *
     * @var array<int, class-string<Subscriber>>
     */
    private const SUBSCRIBERS = [
        DisablePhpCodeCoverageIfNotRequired::class,
        PrepareForInitialTestRun::class,
        EnsureToRunMutationTestingIfRequired::class,
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
        foreach (self::SUBSCRIBERS as $subscriber) {
            $instance = $this->container->get($subscriber);

            assert($instance instanceof Subscriber);

            Event\Facade::instance()->registerSubscriber($instance);
        }
    }
}
