<?php

declare(strict_types=1);

use Pest\Mutate\Factories\ProfileFactory;

// @codeCoverageIgnoreStart
if (! function_exists('mutate')) {
    // @codeCoverageIgnoreEnd

    /**
     * Returns a factory to configure the mutation testing profile.
     */
    function mutate(string $profile = 'default'): ProfileFactory
    {
        return new ProfileFactory($profile);
    }
}
