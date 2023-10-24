<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Mutate\Options\CoveredOnlyOption;
use Pest\Mutate\Options\MinMsiOption;
use Pest\Mutate\Options\MutateOption;
use Pest\Plugins\Concerns\HandleArguments;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @final
 */
class Plugin implements HandlesArguments
{
    use HandleArguments;

    public Config $config;

    private const OPTIONS = [
        MutateOption::class,
        MinMsiOption::class,
        CoveredOnlyOption::class,
    ];

    /**
     * Creates a new Plugin instance.
     */
    public function __construct(
        private readonly OutputInterface $output
    ) {
        $this->config = new Config();
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

        if ($input->hasOption(MinMsiOption::ARGUMENT)) {
            $this->config->minMSI = (float) $input->getOption(MinMsiOption::ARGUMENT); // @phpstan-ignore-line
        }

        if ($input->hasOption(CoveredOnlyOption::ARGUMENT)) {
            $this->config->coveredOnly = $input->getOption(CoveredOnlyOption::ARGUMENT) !== 'false';
        }

        if (! $input->hasOption(MutateOption::ARGUMENT)) {
            return $arguments;
        }

        $this->runMutationTesting();
    }

    private function runMutationTesting(): never
    {
        $this->output->writeln('Running mutation tests...');

        $exitCode = 0;

        $this->exit($exitCode);
    }

    /**
     * Exits the process with the given code.
     */
    private function exit(int $code): never
    {
        exit($code);
    }
}
