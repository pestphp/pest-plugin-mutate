<?php

declare(strict_types=1);

use Pest\Mutate\Mutation;
use Pest\Mutate\MutationTest;
use Pest\Mutate\Mutators\Equality\GreaterOrEqualToGreater;
use Pest\Mutate\Repositories\TelemetryRepository;
use Pest\Mutate\Support\Configuration\Configuration;
use Pest\Mutate\Support\MutationTestResult;
use Pest\Support\Container;
use Symfony\Component\Finder\SplFileInfo;

beforeEach(function (): void {
    $telemetryRepository = new TelemetryRepository;
    $telemetryRepository->initialTestSuiteDuration(1);

    Container::getInstance()->add(TelemetryRepository::class, $telemetryRepository);

    $this->script = (string) tempnam(sys_get_temp_dir(), 'pest-mutate-script-');
    $this->argumentsSeen = (string) tempnam(sys_get_temp_dir(), 'pest-mutate-arguments-');

    // stands in for the Pest script: records the arguments it was given, then passes
    file_put_contents($this->script, sprintf(
        '<?php file_put_contents(%s, serialize($argv)); exit(0);',
        var_export($this->argumentsSeen, true),
    ));
});

afterEach(function (): void {
    @unlink($this->script);
    @unlink($this->argumentsSeen);
});

/**
 * Runs one mutant whose original arguments are the given ones, and returns the arguments
 * its subprocess actually received.
 *
 * @param  array<int, string>  $originalArguments
 * @return array<int, string>
 */
function runMutantWith(array $originalArguments, string $argumentsSeen): array
{
    $file = new SplFileInfo(__DIR__.'/../Fixtures/Classes/SizeHelper.php', '', '');

    $mutationTest = new MutationTest(new Mutation(
        file: $file,
        id: 'mutation-id',
        mutator: GreaterOrEqualToGreater::class,
        startLine: 15,
        endLine: 15,
        diff: '',
        modifiedSourcePath: $file->getRealPath(),
    ));

    $started = $mutationTest->start(
        coveredLines: [
            $file->getRealPath() => [
                15 => ['P\Tests\Unit\SizeHelperTest::__pest_evaluable_it_is_big'],
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
        originalArguments: $originalArguments,
    );

    expect($started)->toBeTrue();

    while (! $mutationTest->hasFinished()) {
        usleep(10_000);
    }

    // the subprocess exits successfully, so the mutant must be reported as escaped —
    // anything else means it never ran the tests it was asked to run
    expect($mutationTest->result())->toBe(MutationTestResult::Untested);

    /** @var array<int, string> $arguments */
    $arguments = unserialize((string) file_get_contents($argumentsSeen));

    return $arguments;
}

it('runs the mutant through the php binary, rather than treating the pest script as executable', function (): void {
    $arguments = runMutantWith([$this->script], $this->argumentsSeen);

    expect($arguments[0])->toBe($this->script)
        ->and($arguments)->toContain('--bail');
});

it('does not pass the paratest only options on to the mutant', function (): void {
    $arguments = runMutantWith([
        $this->script,
        '--processes=2',
        '--passthru-php=-d pcov.enabled=1',
        '--coverage-php='.sys_get_temp_dir().'/coverage.php',
    ], $this->argumentsSeen);

    expect($arguments)
        ->not->toContain('--processes=2')
        ->not->toContain('--passthru-php=-d pcov.enabled=1')
        ->not->toContain('--coverage-php='.sys_get_temp_dir().'/coverage.php');
});
