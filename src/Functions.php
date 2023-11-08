<?php

declare(strict_types=1);

use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Support\Configuration\GlobalConfiguration;
use Pest\Support\Container;

// @codeCoverageIgnoreStart
if (! function_exists('mutate')) {
    // @codeCoverageIgnoreEnd

    /**
     * Returns a factory to configure the mutation testing profile.
     */
    function mutate(string $profile = 'default'): GlobalConfiguration
    {
        return Container::getInstance()->get(ConfigurationRepository::class)->globalConfiguration($profile); // @phpstan-ignore-line
    }
}
