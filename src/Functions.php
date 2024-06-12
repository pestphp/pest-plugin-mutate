<?php

declare(strict_types=1);
use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Support\Configuration\GlobalConfiguration;
use Pest\PendingCalls\BeforeEachCall;
use Pest\Support\Backtrace;
use Pest\Support\Container;
use Pest\TestSuite;

// @codeCoverageIgnoreStart
if (! function_exists('mutate')) {
    // @codeCoverageIgnoreEnd

    /**
     * Returns a factory to configure the mutation testing profile.
     */
    function mutate(string $profile = 'default'): GlobalConfiguration
    {
        try {
            if (! str_ends_with(Backtrace::testFile(), 'Pest.php')) {
                Container::getInstance()->get(MutationTestRunner::class)->enable(); // @phpstan-ignore-line

                (new BeforeEachCall(TestSuite::getInstance(), Backtrace::testFile(), fn () => null))->only(); // @phpstan-ignore-line
            }
        } catch (Throwable) { // @phpstan-ignore-line
            // @ignoreException
        }

        return Container::getInstance()->get(ConfigurationRepository::class)->globalConfiguration($profile); // @phpstan-ignore-line
    }
}
