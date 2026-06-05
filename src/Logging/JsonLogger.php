<?php

declare( strict_types=1 );

namespace Pest\Mutate\Logging;

use Pest\Mutate\Contracts\Logger;
use Pest\Mutate\MutationSuite;

/**
 * @internal
 *
 * @final
 */
class JsonLogger implements Logger
{
    private string $outputPath;

    public function __construct( string $outputPath )
    {
        $this->outputPath = $outputPath;
    }

    public function mutationSuiteFinished( MutationSuite $mutationSuite ): void
    {
        $json = json_encode( [
            'format'  => 'pest',
            'results' => array_values( array_map( function ( $testCollection ) {
                return [
                    'path'            => $testCollection->file->getRealPath(),
                    'count_total'     => $testCollection->count(),
                    'count_not_run'   => $testCollection->notRun(),
                    'count_timed_out' => $testCollection->timedOut(),
                    'count_uncovered' => $testCollection->uncovered(),
                    'count_untested'  => $testCollection->untested(),
                    'tests'           => array_map( function ( $test ) {
                        return [
                            'id'       => $test->getId(),
                            'duration' => round( $test->duration(), 4 ),
                            'result'   => $test->result()->value,
                            'mutation' => [
                                'id'         => $test->mutation->id,
                                'mutator'    => $test->mutation->mutator,
                                'start_line' => $test->mutation->startLine,
                                'end_line'   => $test->mutation->endLine,
                            ],
                        ];
                    }, $testCollection->tests() )
                ];
            }, $mutationSuite->repository->all() ) ),
            'stats'   => [
                'duration' => round( $mutationSuite->duration(), 4 ),
                'score'    => $mutationSuite->repository->score(),
                'tests'    => [
                    'count_total'     => $mutationSuite->repository->count(),
                    'count_not_run'   => $mutationSuite->repository->notRun(),
                    'count_timed_out' => $mutationSuite->repository->timedOut(),
                    'count_uncovered' => $mutationSuite->repository->uncovered(),
                    'count_untested'  => $mutationSuite->repository->untested(),
                ],
            ],
        ], JSON_THROW_ON_ERROR );
        file_put_contents( $this->outputPath, $json );
    }
}
