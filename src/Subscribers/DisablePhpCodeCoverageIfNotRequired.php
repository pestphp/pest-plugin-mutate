<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Support\Container;
use PHPUnit\Event\TestSuite\Loaded;
use PHPUnit\Event\TestSuite\LoadedSubscriber;
use PHPUnit\Runner\CodeCoverage;

/**
 * @internal
 */
final class DisablePhpCodeCoverageIfNotRequired implements LoadedSubscriber
{
    /**
     * Runs the subscriber.
     */
    public function notify(Loaded $event): void
    {
        /** @var MutationTestRunner $mutationTestRunner */
        $mutationTestRunner = Container::getInstance()->get(MutationTestRunner::class);
        if ($mutationTestRunner->isEnabled()) {
            return;
        }
        if ($mutationTestRunner->isCodeCoverageRequested()) {
            return;
        }
        CodeCoverage::instance()->deactivate();
    }
}
