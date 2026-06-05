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
class NullLogger implements Logger
{
    /**
     * @param string $outputPath
     */
    public function __construct( string $outputPath = '' )
    {
        //
    }

    public function mutationSuiteFinished( MutationSuite $mutationSuite ): void {
        //
    }
}
