<?php

declare( strict_types=1 );

use Pest\Mutate\Mutation;
use Pest\Mutate\MutationSuite;
use Pest\Mutate\MutationTest;
use Pest\Mutate\MutationTestCollection;
use Pest\Mutate\Mutators\Equality\EqualToIdentical;
use Pest\Mutate\Support\MutationTestResult;
use Pest\Mutate\Support\Printers\DefaultPrinter;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\SplFileInfo;

afterEach( function (): void {
    @unlink( __DIR__ . '/mutation-output.json' );
} );

test( 'it can output to json', function () {
    $logger = new \Pest\Mutate\Logging\JsonLogger( __DIR__ . '/mutation-output.json' );

    $suite = new MutationSuite();
    $suite->repository->add( new Mutation(
        id: 'test-id',
        file: new SplFileInfo( 'test.php', '', '' ),
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
    ) );
    $suite->repository->all()[0]->tests()[0]->updateResult( MutationTestResult::Tested );
    $suite->trackStart();
    $suite->trackFinish();

    $logger->mutationSuiteFinished( $suite );

    expect( __DIR__ . '/mutation-output.json' )->toBeReadableFile();

    expect( file_get_contents( __DIR__ . '/mutation-output.json' ) )->json()->toMatchArray( [
        'format'  => 'pest',
        'results' =>
            [
                [
                    'path'            => false,
                    'count_total'     => 1,
                    'count_not_run'   => 0,
                    'count_timed_out' => 0,
                    'count_uncovered' => 0,
                    'count_untested'  => 0,
                    'tests'           =>
                        [
                            0 =>
                                [
                                    'id'       => 'test-id',
                                    'duration' => 0,
                                    'result'   => 'tested',
                                    'mutation' =>
                                        [
                                            'id'         => 'test-id',
                                            'mutator'    => 'Pest\\Mutate\\Mutators\\Equality\\EqualToIdentical',
                                            'start_line' => 4,
                                            'end_line'   => 4,
                                        ],
                                ],
                        ],
                ],
            ],
        'stats'   =>
            [
                'duration' => 0,
                'score'    => 100,
                'tests'    =>
                    [
                        'count_total'     => 1,
                        'count_not_run'   => 0,
                        'count_timed_out' => 0,
                        'count_uncovered' => 0,
                        'count_untested'  => 0,
                    ],
            ],
    ] );
} );
