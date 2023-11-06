<?php

namespace Pest\Mutate;

use Pest\Mutate\Event\Facade;
use Pest\Mutate\Plugins\Mutate;
use Pest\Mutate\Support\MutationTestResult;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use function Termwind\render;

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

    public function run($coveredLines, $profile, $originalArguments): void
    {
        /** @var string $tmpfname */
        $tmpfname = tempnam('/tmp', 'pest_mutation_');
        file_put_contents($tmpfname, $this->mutation->modifiedSource());

        // TODO: we should pass the tests to run in another way, maybe via cache, mutation or env variable
        $filters = [];
        foreach (range($this->mutation->originalNode->getStartLine(), $this->mutation->originalNode->getEndLine()) as $lineNumber) {
            foreach ($coveredLines[$this->mutation->file->getRealPath()][$lineNumber] ?? [] as $test) {
                preg_match('/\\\\([a-zA-Z0-9]*)::__pest_evaluable_([^#]*)"?/', (string) $test, $matches);
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
                $profile->parallel ? '--parallel' : '',
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
            $this->updateResult->updateResult(MutationTestResult::Timeout);
            Facade::instance()->emitter()->mutationTimedOut($this);

            return;
        }

        if ($process->isSuccessful()) {
            $this->updateResult(MutationTestResult::Survived);
            Facade::instance()->emitter()->mutationSurvived($this);

            $path = str_ireplace(getcwd().'/', '', $this->mutation->file->getRealPath());

            $diff = <<<HTML
                    <div class="text-green">+ {$this->mutation->diff()['modified'][0]}</div>
                    <div class="text-red">- {$this->mutation->diff()['original'][0]}</div>
                    HTML;

            render(<<<HTML
                        <div class="mx-2 flex">
                            <span>at {$path}:{$this->mutation->originalNode->getLine()} </span>
                            <span class="flex-1 content-repeat-[.] text-gray mx-1"></span>
                            <span>{$this->mutation->mutator::name()}</span>
                        </div>
                    HTML
            );

            render(<<<HTML
                        <div class="mx-2 mb-1 flex">
                            {$diff}
                        </div>
                    HTML
            );

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
