<?php

namespace Pest\Mutate\Event;

use Pest\Mutate\Contracts\Subscriber;
use ReflectionClass;

class Facade
{
    private static ?self $instance = null;

    private array $subscribers = [];

    public static function instance(): self
    {
        if(!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function subscribers(): array
    {
        return $this->subscribers;
    }

    public function emitter(): Emitter
    {
        return Emitter::instance();
    }

    public function registerSubscriber(Subscriber $subscriber): void
    {
        $reflection = new ReflectionClass($subscriber);

        $this->subscribers[$reflection->getInterfaceNames()[0]][] = $subscriber;
    }
}
