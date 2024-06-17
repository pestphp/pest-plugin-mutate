<?php

declare(strict_types=1);

use Pest\Mutate\Mutation;
use Pest\Mutate\MutationSuite;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;
use Pest\Mutate\Mutators\Equality\EqualToIdentical;
use Pest\Mutate\Support\Printers\DefaultPrinter;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\SplFileInfo;

beforeEach(function (): void {
    $this->out = new BufferedOutput();
    $this->printer = new DefaultPrinter($this->out);

    $this->createMutation = fn (): Mutation => new Mutation(
        id: 'test-id',
        file: new SplFileInfo('test.php', '', ''),
        mutator: EqualToIdentical::class,
        startLine: 4,
        endLine: 4,
        diff: <<<'DIFF'
            --- Expected
            +++ Actual
            @@ @@
              <fg=gray></>
              <fg=red>-     return 1 == '1';</>
              <fg=green>+   return 1 === '1';</>
              <fg=gray></>
            DIFF,
        modifiedSourcePath: 'test-modified.php',
    );
});

describe('print mutation test', function (): void {
    beforeEach(function (): void {
        $this->mutationTest = new MutationTest(($this->createMutation)());
    });

    it('reports a killed mutation in compact mode', function (): void {
        $this->printer->compact();

        $this->printer->reportKilledMutation($this->mutationTest);

        expect($this->out->fetch())
            ->toBe('.');
    });

    it('reports a killed mutation in normal mode', function (): void {
        $this->printer->reportKilledMutation($this->mutationTest);

        expect($this->out->fetch())
            ->toContain('✓', 'Line 4:', 'EqualToIdentical')
            ->toEndWith(PHP_EOL)
            ->toMatchSnapshot();
    });

    it('reports a escaped mutation in compact mode', function (): void {
        $this->printer->compact();

        $this->printer->reportEscapedMutation($this->mutationTest);

        expect($this->out->fetch())
            ->toBe('x');
    });

    it('reports a escaped mutation in normal mode', function (): void {
        $this->printer->reportEscapedMutation($this->mutationTest);

        expect($this->out->fetch())
            ->toContain('⨯', 'Line 4:', 'EqualToIdentical')
            ->toEndWith(PHP_EOL)
            ->toMatchSnapshot();
    });

    it('reports a not covered mutation in compact mode', function (): void {
        $this->printer->compact();

        $this->printer->reportNotCoveredMutation($this->mutationTest);

        expect($this->out->fetch())
            ->toBe('-');
    });

    it('reports a not covered mutation in normal mode', function (): void {
        $this->printer->reportNotCoveredMutation($this->mutationTest);

        expect($this->out->fetch())
            ->toContain('-', 'Line 4:', 'EqualToIdentical')
            ->toEndWith(PHP_EOL)
            ->toMatchSnapshot();
    });

    it('reports a timed out mutation in compact mode', function (): void {
        $this->printer->compact();

        $this->printer->reportTimedOutMutation($this->mutationTest);

        expect($this->out->fetch())
            ->toBe('t');
    });

    it('reports a timed out mutation in normal mode', function (): void {
        $this->printer->reportTimedOutMutation($this->mutationTest);

        expect($this->out->fetch())
            ->toContain('t', 'Line 4:', 'EqualToIdentical')
            ->toEndWith(PHP_EOL)
            ->toMatchSnapshot();
    });
});

describe('print mutation test collection', function (): void {
    beforeEach(function (): void {
        $this->mutationTestCollection = new MutationTestCollection(
            file: new SplFileInfo(dirname(__DIR__, 3).'/Fixtures/Classes/AgeHelper.php', '', ''),
        );
    });

    it('does not print the file name in compact mode', function (): void {
        $this->printer->compact();

        $this->printer->printFilename($this->mutationTestCollection);

        expect($this->out->fetch())
            ->toBe('');
    });

    it('prints the name of the file in normal mode', function (): void {
        $this->printer->printFilename($this->mutationTestCollection);

        expect($this->out->fetch())
            ->toStartWith(PHP_EOL)
            ->toContain('RUN', 'tests/Fixtures/Classes/AgeHelper.php')
            ->toMatchSnapshot();
    });
});

it('reports an error', function (): void {
    $this->printer->reportError('Error message');

    expect($this->out->fetch())
        ->toContain('ERROR', 'Error message')
        ->toMatchSnapshot();
});

it('reports a score not reached message', function (): void {
    $this->printer->reportScoreNotReached(95.1, 98.2);

    expect($this->out->fetch())
        ->toContain('FAIL', 'Mutation score below expected', '95.1 %', 'Minimum', '98.2 %')
        ->toMatchSnapshot();
});

describe('mutation suite', function (): void {
    beforeEach(function (): void {
        $this->mutationSuite = new MutationSuite();
    });

    it('reports the start of mutation generation', function (): void {
        $this->printer->reportMutationGenerationStarted($this->mutationSuite);

        expect($this->out->fetch())
            ->toContain('Generating mutations')
            ->toMatchSnapshot();
    });

    it('reports the number of mutations created', function (): void {
        $this->mutationSuite->repository->add(($this->createMutation)());
        $this->mutationSuite->repository->add(($this->createMutation)());

        $this->printer->reportMutationGenerationFinished($this->mutationSuite);

        expect($this->out->fetch())
            ->toContain('2 Mutations for 1 Files created')
            ->toEndWith(PHP_EOL)
            ->toMatchSnapshot();
    });

    it('reports the start of running the mutation suite', function (): void {
        $this->printer->reportMutationSuiteStarted($this->mutationSuite);

        expect($this->out->fetch())
            ->toContain('Running mutation tests:')
            ->toEndWith(PHP_EOL)
            ->toMatchSnapshot();
    });

    it('reports the start of running the mutation suite in compact mode', function (): void {
        $this->printer->compact();

        $this->printer->reportMutationSuiteStarted($this->mutationSuite);

        expect($this->out->fetch())
            ->toContain('Running mutation tests:')
            ->toContain(PHP_EOL.PHP_EOL)
            ->toEndWith('  ')
            ->toMatchSnapshot();
    });

    it('reports the end of running the mutation suite', function (): void {
        $this->mutationSuite->trackStart();
        $this->mutationSuite->trackFinish();

        $this->printer->reportMutationSuiteFinished($this->mutationSuite);

        expect(preg_replace('/\n  Parallel:  \d* processes/', '', $this->out->fetch()))
            ->toContain('Mutations:', 'Score:', 'Duration:', 'INFO', 'No mutations created.')
            ->toEndWith(PHP_EOL)
            ->toMatchSnapshot();
    });

    it('starts with a new line when reporting the end of running the mutation suite in compact mode', function (): void {
        $this->mutationSuite->trackStart();
        $this->mutationSuite->trackFinish();
        $this->printer->compact();

        $this->printer->reportMutationSuiteFinished($this->mutationSuite);

        expect($this->out->fetch())
            ->toStartWith(PHP_EOL);
    });
});
