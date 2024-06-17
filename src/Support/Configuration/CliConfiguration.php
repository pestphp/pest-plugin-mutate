<?php

declare(strict_types=1);

namespace Pest\Mutate\Support\Configuration;

use Pest\Mutate\Cache\NullStore;
use Pest\Mutate\Options\BailOption;
use Pest\Mutate\Options\ChangedOnlyOption;
use Pest\Mutate\Options\ClassOption;
use Pest\Mutate\Options\ClearCache;
use Pest\Mutate\Options\CoveredOnlyOption;
use Pest\Mutate\Options\ExceptOption;
use Pest\Mutate\Options\IgnoreMinScoreOnZeroMutationsOption;
use Pest\Mutate\Options\IgnoreOption;
use Pest\Mutate\Options\MinScoreOption;
use Pest\Mutate\Options\MutateOption;
use Pest\Mutate\Options\MutationIdOption;
use Pest\Mutate\Options\MutatorsOption;
use Pest\Mutate\Options\NoCache;
use Pest\Mutate\Options\ParallelOption;
use Pest\Mutate\Options\PathOption;
use Pest\Mutate\Options\ProcessesOption;
use Pest\Mutate\Options\ProfileOption;
use Pest\Mutate\Options\RetryOption;
use Pest\Mutate\Options\StopOnNotCoveredOption;
use Pest\Mutate\Options\StopOnEscapedOption;
use Pest\Mutate\Options\UncommittedOnlyOption;
use Pest\Support\Container;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

class CliConfiguration extends AbstractConfiguration
{
    private const OPTIONS = [
        MutateOption::class,
        ClassOption::class,
        CoveredOnlyOption::class,
        MinScoreOption::class,
        IgnoreMinScoreOnZeroMutationsOption::class,
        MutatorsOption::class,
        ExceptOption::class,
        PathOption::class,
        IgnoreOption::class,
        ParallelOption::class,
        ProcessesOption::class,
        ProfileOption::class,
        StopOnEscapedOption::class,
        StopOnNotCoveredOption::class,
        BailOption::class,
        UncommittedOnlyOption::class,
        ChangedOnlyOption::class,
        RetryOption::class,
        MutationIdOption::class,
        NoCache::class,
        ClearCache::class,
    ];

    /**
     * @param  array<int, string>  $arguments
     * @return array<int, string>
     */
    public function fromArguments(array $arguments): array
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

        $input = new ArgvInput($filteredArguments, new InputDefinition($inputOptions));

        if ($input->hasOption(CoveredOnlyOption::ARGUMENT)) {
            $this->coveredOnly($input->getOption(CoveredOnlyOption::ARGUMENT) !== 'false');
        }

        if ($input->hasOption(PathOption::ARGUMENT)) {
            $this->path(explode(',', (string) $input->getOption(PathOption::ARGUMENT))); // @phpstan-ignore-line
        }

        if ($input->hasOption(IgnoreOption::ARGUMENT)) {
            $this->ignore(explode(',', (string) $input->getOption(IgnoreOption::ARGUMENT))); // @phpstan-ignore-line
        }

        if ($input->hasOption(MutatorsOption::ARGUMENT)) {
            $this->mutator(explode(',', (string) $input->getOption(MutatorsOption::ARGUMENT))); // @phpstan-ignore-line
        }

        if ($input->hasOption(ExceptOption::ARGUMENT)) {
            $this->except(explode(',', (string) $input->getOption(ExceptOption::ARGUMENT))); // @phpstan-ignore-line
        }

        if ($input->hasOption(MinScoreOption::ARGUMENT)) {
            $this->min((float) $input->getOption(MinScoreOption::ARGUMENT)); // @phpstan-ignore-line
        }

        if ($input->hasOption(IgnoreMinScoreOnZeroMutationsOption::ARGUMENT)) {
            $this->ignoreMinScoreOnZeroMutations($input->getOption(IgnoreMinScoreOnZeroMutationsOption::ARGUMENT) !== 'false');
        }

        if ($input->hasOption(CoveredOnlyOption::ARGUMENT)) {
            $this->coveredOnly($input->getOption(CoveredOnlyOption::ARGUMENT) !== 'false');
        }

        if ($input->hasOption(ParallelOption::ARGUMENT)) {
            $this->parallel();

            if (($index = array_search('--parallel', $_SERVER['argv'], true)) !== false) {
                unset($_SERVER['argv'][$index]);
            }
        }

        if ($input->hasOption(ProcessesOption::ARGUMENT)) {
            $this->processes($input->getOption(ProcessesOption::ARGUMENT) !== null ? (int) $input->getOption(ProcessesOption::ARGUMENT) : null); // @phpstan-ignore-line
        }

        if ($input->hasOption(ProfileOption::ARGUMENT)) {
            $this->profile($input->getOption(ProfileOption::ARGUMENT) !== 'false');
        }

        if ($_SERVER['COLLISION_PRINTER_PROFILE'] ?? false) {
            $this->profile(true);
            unset($_SERVER['COLLISION_PRINTER_PROFILE']);
        }

        if ($input->hasOption(ClassOption::ARGUMENT)) {
            $this->class(explode(',', (string) $input->getOption(ClassOption::ARGUMENT))); // @phpstan-ignore-line
        }

        if ($input->hasOption(StopOnEscapedOption::ARGUMENT)) {
            $this->stopOnEscaped($input->getOption(StopOnEscapedOption::ARGUMENT) !== 'false');
        }

        if ($input->hasOption(StopOnNotCoveredOption::ARGUMENT)) {
            $this->stopOnNotCovered($input->getOption(StopOnNotCoveredOption::ARGUMENT) !== 'false');
        }

        if ($input->hasOption(BailOption::ARGUMENT)) {
            $this->stopOnEscaped();
            $this->stopOnNotCovered();
        }

        if ($input->hasOption(UncommittedOnlyOption::ARGUMENT)) {
            $this->uncommittedOnly($input->getOption(UncommittedOnlyOption::ARGUMENT) !== 'false');
        }

        if ($input->hasOption(ChangedOnlyOption::ARGUMENT)) {
            $this->changedOnly($input->getOption(ChangedOnlyOption::ARGUMENT) !== null ? (string) $input->getOption(ChangedOnlyOption::ARGUMENT) : 'main'); // @phpstan-ignore-line
        }

        if ($input->hasOption(RetryOption::ARGUMENT)) {
            $this->retry($input->getOption(RetryOption::ARGUMENT) !== 'false');
        }

        if ($input->hasOption(MutationIdOption::ARGUMENT)) {
            $this->mutationId((string) $input->getOption(MutationIdOption::ARGUMENT)); // @phpstan-ignore-line
        }

        if ($input->hasOption(NoCache::ARGUMENT)) {
            Container::getInstance()->add(CacheInterface::class, new NullStore());
        }

        if ($input->hasOption(ClearCache::ARGUMENT)) {
            Container::getInstance()->get(CacheInterface::class)->clear(); // @phpstan-ignore-line
        }

        return $arguments;
    }
}
