<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Event\Facade;
use Pest\Mutate\Plugins\Mutate;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Support\Configuration\Configuration;
use Pest\Mutate\Support\MutationTestResult;
use Pest\Support\Container;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class MutationTest
{
    private MutationTestResult $result = MutationTestResult::None;

    private Process $process;

    public function __construct(public readonly Mutation $mutation)
    {
    }

    public function result(): MutationTestResult
    {
        return $this->result;
    }

    public function updateResult(MutationTestResult $result): void
    {
        $this->result = $result;
    }

    /**
     * @param  array<string, array<int, array<int, string>>>  $coveredLines
     * @param  array<int, string>  $originalArguments
     */
    public function start(array $coveredLines, Configuration $configuration, array $originalArguments): bool
    {
        // TODO: we should pass the tests to run in another way, maybe via cache, mutation or env variable
        $filters = [];
        foreach (range($this->mutation->startLine, $this->mutation->endLine) as $lineNumber) {
            foreach ($coveredLines[$this->mutation->file->getRealPath()][$lineNumber] ?? [] as $test) {
                preg_match('/\\\\([a-zA-Z0-9]*)::__pest_evaluable_([^#]*)"?/', $test, $matches);
                $filters[] = $matches[1].'::'.preg_replace(['/_([a-z])_/', '/([^_])_([^_])/', '/__/'], [' $1 ', '$1 $2', '_'], $matches[2]);
            }
        }
        $filters = array_unique($filters);

        if ($filters === []) {
            $this->updateResult(MutationTestResult::NotCovered);

            Facade::instance()->emitter()->mutationNotCovered($this);

            return false;
        }

        // TODO: filter arguments to remove unnecessary stuff (Teamcity, Coverage, etc.)
        $process = new Process(
            command: [
                ...$originalArguments,
                '--bail',
                '--filter="'.implode('|', $filters).'"',
            ],
            env: [
                Mutate::ENV_MUTATION_TESTING => $this->mutation->file->getRealPath(),
                Mutate::ENV_MUTATION_FILE => $this->mutation->modifiedSourcePath,
            ],
            timeout: $this->calculateTimeout(),
        );

        $process->start();

        $this->process = $process;

        return true;
    }

    private function calculateTimeout(): int
    {
        // TODO: calculate a reasonable timeout
        return Container::getInstance()->get(ConfigurationRepository::class)->mergedConfiguration()->parallel ? // @phpstan-ignore-line
            10 :
            3;
    }

    public function hasFinished(): bool
    {
        try {
            if ($this->process->isRunning()) {
                $this->process->checkTimeout();

                return false;
            }
        } catch (ProcessTimedOutException) {
            $this->updateResult(MutationTestResult::Timeout);

            Facade::instance()->emitter()->mutationTimedOut($this);

            return true;
        }

        if ($this->process->isSuccessful()) {
            $this->updateResult(MutationTestResult::Survived);

            Facade::instance()->emitter()->mutationSurvived($this);

            return true;
        }

        $this->updateResult(MutationTestResult::Killed);

        Facade::instance()->emitter()->mutationKilled($this);

        return true;
    }
}
