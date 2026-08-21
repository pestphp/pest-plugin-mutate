<?php

declare(strict_types=1);

use Pest\Mutate\Mutation;
use Pest\Mutate\MutationTest;
use Pest\Mutate\Repositories\TelemetryRepository;
use Pest\Mutate\Support\Configuration\Configuration;
use Pest\Mutate\Support\MutationTestResult;
use Pest\Support\Container;
use Symfony\Component\Finder\SplFileInfo;

it('does not report an errored mutation child process as tested', function (): void {
    $sourcePath = tempnam(sys_get_temp_dir(), 'pest-mutate-source-');
    $modifiedSourcePath = tempnam(sys_get_temp_dir(), 'pest-mutate-modified-');

    expect($sourcePath)->toBeString()
        ->and($modifiedSourcePath)->toBeString();

    file_put_contents($sourcePath, "<?php\nreturn true;\n");
    file_put_contents($modifiedSourcePath, "<?php\nreturn false;\n");

    $telemetryRepository = new TelemetryRepository;
    $telemetryRepository->initialTestSuiteDuration(0.1);
    Container::getInstance()->add(TelemetryRepository::class, $telemetryRepository);

    $mutation = new Mutation(
        file: new SplFileInfo($sourcePath, '', ''),
        id: 'errored-mutation',
        mutator: 'FakeMutator',
        startLine: 2,
        endLine: 2,
        diff: '',
        modifiedSourcePath: $modifiedSourcePath,
    );

    $mutationTest = new MutationTest($mutation);

    $started = $mutationTest->start(
        coveredLines: [
            $mutation->file->getRealPath() => [
                2 => ['Tests\\Unit\\MutationTestTest::it fails inside the child process'],
            ],
        ],
        configuration: new Configuration(
            coveredOnly: false,
            paths: [],
            pathsToIgnore: [],
            mutators: [],
            classes: [],
            parallel: false,
            processes: 1,
            profile: false,
            minScore: null,
            ignoreMinScoreOnZeroMutations: false,
            stopOnUntested: false,
            stopOnUncovered: false,
            mutationId: null,
            retry: false,
            everything: false,
        ),
        originalArguments: [
            'php',
            '-r',
            'fwrite(STDERR, "child error"); exit(2);',
            '--',
        ],
    );

    expect($started)->toBeTrue();

    while (! $mutationTest->hasFinished()) {
        usleep(1_000);
    }

    expect($mutationTest->result())->not->toBe(MutationTestResult::Tested);
});
