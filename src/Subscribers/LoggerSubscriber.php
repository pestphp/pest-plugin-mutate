<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\Logger;

/**
 * @internal
 */
abstract class LoggerSubscriber
{
    public function __construct(private readonly Logger $logger) {}

    protected function logger(): Logger
    {
        return $this->logger;
    }
}
