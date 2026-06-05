<?php

 declare( strict_types=1 );

 namespace Pest\Mutate\Contracts;

 use Pest\Mutate\MutationSuite;

 /**
  * @internal
  *
  * @final
  */
 interface Logger
 {
     /**
      * @param string $outputPath
      */
     public function __construct( string $outputPath );

     public function mutationSuiteFinished( MutationSuite $mutationSuite ): void;
 }
