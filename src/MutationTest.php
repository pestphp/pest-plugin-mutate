<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Mutate\Event\Facade;
use Pest\Mutate\Plugins\Mutate;
use Pest\Mutate\Support\Configuration\Configuration;
use Pest\Mutate\Support\MutationTestResult;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class MutationTest
{
    private MutationTestResult $result;

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
    public function run(array $coveredLines, Configuration $configuration, array $originalArguments): void
    {
        /** @var string $tmpfname */
        $tmpfname = tempnam('/tmp', 'pest_mutation_');
        file_put_contents($tmpfname, $this->mutation->modifiedSource());

        // TODO: we should pass the tests to run in another way, maybe via cache, mutation or env variable
        $filters = [];
        foreach (range($this->mutation->originalNode->getStartLine(), $this->mutation->originalNode->getEndLine()) as $lineNumber) {
            foreach ($coveredLines[$this->mutation->file->getRealPath()][$lineNumber] ?? [] as $test) {
                preg_match('/\\\\([a-zA-Z0-9]*)::__pest_evaluable_([^#]*)"?/', $test, $matches);
                $filters[] = $matches[1].'::'.preg_replace(['/_([a-z])_/', '/([^_])_([^_])/', '/__/'], [' $1 ', '$1 $2', '_'], $matches[2]);
            }
        }
        $filters = array_unique($filters);

        if ($filters === []) {
            $this->updateResult(MutationTestResult::NotCovered);

            Facade::instance()->emitter()->mutationNotCovered($this);

            return;
        }

        // TODO: filter arguments to remove unnecessary stuff (Teamcity, Coverage, etc.)
        $process = new Process(
            command: [
                ...$originalArguments,
                '--bail',
                '--filter="'.implode('|', $filters).'"',
                $configuration->parallel ? '--parallel' : '',
            ],
            env: [
                Mutate::ENV_MUTATION_TESTING => $this->mutation->file->getRealPath(),
                Mutate::ENV_MUTATION_FILE => $tmpfname,
            ],
            timeout: $this->calculateTimeout(),
        );

        try {
            $process->run();
        } catch (ProcessTimedOutException) {
            $this->updateResult(MutationTestResult::Timeout);

            Facade::instance()->emitter()->mutationTimedOut($this);

            return;
        }

        if ($process->isSuccessful()) {
            $this->updateResult(MutationTestResult::Survived);

            Facade::instance()->emitter()->mutationSurvived($this);

            return;
        }

        $this->updateResult(MutationTestResult::Killed);

        Facade::instance()->emitter()->mutationKilled($this);
    }

    private function calculateTimeout(): int
    {
        // TODO: calculate a reasonable timeout
        return 3;
    }
}
