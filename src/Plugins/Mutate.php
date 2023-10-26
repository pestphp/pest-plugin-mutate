<?php

declare(strict_types=1);

namespace Pest\Mutate\Plugins;

use Pest\Contracts\Plugins\AddsOutput;
use Pest\Contracts\Plugins\Bootable;
use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Factories\ProfileFactory;
use Pest\Mutate\Options\CoveredOnlyOption;
use Pest\Mutate\Options\MinMsiOption;
use Pest\Mutate\Options\MutateOption;
use Pest\Mutate\Options\MutatorsOption;
use Pest\Mutate\Options\PathsOption;
use Pest\Mutate\Subscribers\EnsureToRunMutationTestingIfRequired;
use Pest\Plugins\Concerns\HandleArguments;
use Pest\Support\Container;
use PHPUnit\Event\Facade;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @final
 */
class Mutate implements Bootable, HandlesArguments
{
    use HandleArguments;

    private const OPTIONS = [
        MutateOption::class,
        PathsOption::class,
        MutatorsOption::class,
        MinMsiOption::class,
        CoveredOnlyOption::class,
    ];

    /**
     * Creates a new Plugin instance.
     */
    public function __construct(
        private readonly OutputInterface $output
    ) {
        //
    }

    public function boot(): void
    {
        $container = Container::getInstance();

        $container->add(MutationTestRunner::class, new \Pest\Mutate\Tester\MutationTestRunner($this->output));

        $subscriber = $container->get(EnsureToRunMutationTestingIfRequired::class);
        Facade::instance()->registerSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function handleArguments(array $arguments): array
    {
        $filteredArguments = ['vendor/bin/pest'];
        $inputOptions = [];
        foreach ($arguments as $key => $argument) {
            foreach (self::OPTIONS as $option) {
                if ($option::match($argument)) {
                    $filteredArguments[] = $argument;
                    $inputOptions[] = $option::inputOption();

                    if ($option::remove()) {
                        unset($arguments[$key]);
                    }
                }
            }
        }
        $inputDefinition = new InputDefinition($inputOptions);

        $input = new ArgvInput($filteredArguments, $inputDefinition);

        if (! $input->hasOption(MutateOption::ARGUMENT)) {
            return $arguments;
        }

        $profileName = $input->getOption(MutateOption::ARGUMENT) ?? 'default';
        $profileFactory = new ProfileFactory($profileName); // @phpstan-ignore-line

        if ($input->hasOption(PathsOption::ARGUMENT)) {
            $profileFactory->paths(explode(',', (string) $input->getOption(PathsOption::ARGUMENT))); // @phpstan-ignore-line
        }

        if ($input->hasOption(MutatorsOption::ARGUMENT)) {
            $profileFactory->mutators(explode(',', (string) $input->getOption(MutatorsOption::ARGUMENT))); // @phpstan-ignore-line
        }

        if ($input->hasOption(MinMsiOption::ARGUMENT)) {
            $profileFactory->min((float) $input->getOption(MinMsiOption::ARGUMENT)); // @phpstan-ignore-line
        }

        if ($input->hasOption(CoveredOnlyOption::ARGUMENT)) {
            $profileFactory->coveredOnly($input->getOption(CoveredOnlyOption::ARGUMENT) !== 'false');
        }

        Container::getInstance()->get(MutationTestRunner::class) // @phpstan-ignore-line
            ->enable($profileName);

        return $arguments;
    }
}
