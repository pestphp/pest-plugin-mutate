<?php

declare(strict_types=1);

namespace Pest\Mutate\Plugins;

use Pest\Contracts\Plugins\AddsOutput;
use Pest\Contracts\Plugins\Bootable;
use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Mutate\Contracts\MutationTester;
use Pest\Mutate\Factories\ProfileFactory;
use Pest\Mutate\Options\CoveredOnlyOption;
use Pest\Mutate\Options\MinMsiOption;
use Pest\Mutate\Options\MutateOption;
use Pest\Mutate\Options\MutatorsOption;
use Pest\Mutate\Options\PathsOption;
use Pest\Plugins\Concerns\HandleArguments;
use Pest\Support\Container;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @final
 */
class Mutate implements AddsOutput, Bootable, HandlesArguments
{
    use HandleArguments;

    private static bool $enabled = false;

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
        Container::getInstance()->add(MutationTester::class, new \Pest\Mutate\Tester\MutationTester($this->output));
    }

    public static function enable(): void
    {
        self::$enabled = true;
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

        $profileFactory = new ProfileFactory($input->getOption(MutateOption::ARGUMENT) ?? 'default'); // @phpstan-ignore-line

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

        $this->runMutationTesting();

        return $arguments;
    }

    private function runMutationTesting(): void
    {
        Container::getInstance()->get(MutationTester::class) // @phpstan-ignore-line
            ->run();
    }

    public function addOutput(int $exitCode): int
    {
        // TODO: this is probably the wrong place to run mutation testing
        if (self::$enabled) {
            $this->runMutationTesting();
        }

        return $exitCode;
    }
}
