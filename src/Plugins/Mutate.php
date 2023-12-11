<?php

declare(strict_types=1);

namespace Pest\Mutate\Plugins;

use Infection\StreamWrapper\IncludeInterceptor;
use Pest\Contracts\Bootstrapper;
use Pest\Contracts\Plugins\Bootable;
use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Mutate\Boostrappers\BootPhpUnitSubscribers;
use Pest\Mutate\Boostrappers\BootSubscribers;
use Pest\Mutate\Cache\FileStore;
use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Plugins\Concerns\HandleArguments;
use Pest\Support\Container;
use Pest\Support\Coverage;
use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 *
 * @final
 */
class Mutate implements Bootable, HandlesArguments
{
    use HandleArguments;

    final public const ENV_MUTATION_TESTING = 'PEST_MUTATION_TESTING';

    final public const ENV_MUTATION_FILE = 'PEST_MUTATION_FILE';

    /**
     * The Kernel bootstrappers.
     *
     * @var array<int, class-string>
     */
    private const BOOTSTRAPPERS = [
        BootPhpUnitSubscribers::class,
        BootSubscribers::class,
    ];

    /**
     * Creates a new Plugin instance.
     */
    public function __construct(
        private readonly Container $container
    ) {
        //
    }

    public function boot(): void
    {
        if (getenv(self::ENV_MUTATION_TESTING) !== false) {
            IncludeInterceptor::intercept(getenv(self::ENV_MUTATION_TESTING), getenv(self::ENV_MUTATION_FILE));
            IncludeInterceptor::enable();
        }

        $this->container->add(MutationTestRunner::class, new \Pest\Mutate\Tester\MutationTestRunner());

        foreach (self::BOOTSTRAPPERS as $bootstrapper) {
            $bootstrapper = Container::getInstance()->get($bootstrapper);
            assert($bootstrapper instanceof Bootstrapper);

            $bootstrapper->boot();
        }

        $this->container->add(CacheInterface::class, new FileStore());
    }

    /**
     * {@inheritdoc}
     */
    public function handleArguments(array $arguments): array
    {
        /** @var \Pest\Mutate\Tester\MutationTestRunner $mutationTestRunner */
        $mutationTestRunner = Container::getInstance()->get(MutationTestRunner::class);

        // always enable php coverage report, but it will be disabled if not required
        if (Coverage::isAvailable()) {
            $coverageRequired = array_filter($arguments, fn (string $argument): bool => str_starts_with($argument, '--coverage')) !== [];
            if ($coverageRequired) {
                $mutationTestRunner->doNotDisableCodeCoverage();
            }
            $arguments[] = '--coverage-php='.Coverage::getPath();
        }

        if (array_filter($arguments, fn (string $argument): bool => str_starts_with($argument, '--mutate=') || $argument === '--mutate') === []) {
            $mutationTestRunner->setOriginalArguments($arguments);

            return $arguments;
        }

        $arguments = Container::getInstance()->get(ConfigurationRepository::class) // @phpstan-ignore-line
            ->cliConfiguration->fromArguments($arguments);

        $mutationTestRunner->enable();
        $mutationTestRunner->setOriginalArguments($arguments);

        return $arguments;
    }
}
