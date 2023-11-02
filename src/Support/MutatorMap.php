<?php

namespace Pest\Mutate\Support;

use Pest\Mutate\Mutators\Sets\ArithmeticSet;
use Pest\Mutate\Mutators\Sets\ArraySet;
use Pest\Mutate\Mutators\Sets\AssignmentSet;
use Pest\Mutate\Mutators\Sets\CastingSet;
use Pest\Mutate\Mutators\Sets\ControlStructuresSet;
use Pest\Mutate\Mutators\Sets\EqualitySet;
use Pest\Mutate\Mutators\Sets\LaravelSet;
use Pest\Mutate\Mutators\Sets\LogicalSet;
use Pest\Mutate\Mutators\Sets\MathSet;
use Pest\Mutate\Mutators\Sets\NumberSet;
use Pest\Mutate\Mutators\Sets\StringSet;

class MutatorMap
{
    static ?array $map = null;

    public static function get(): array
    {
           if(self::$map !== null) {
               return self::$map;
           }

           $mutators =  [
               ...ArithmeticSet::mutators(),
               ...ArraySet::mutators(),
               ...AssignmentSet::mutators(),
               ...CastingSet::mutators(),
               ...ControlStructuresSet::mutators(),
               ...EqualitySet::defaultMutators(),
               ...LogicalSet::mutators(),
               ...LaravelSet::mutators(),
               ...MathSet::mutators(),
               ...NumberSet::mutators(),
               ...StringSet::mutators(),
           ];

           $map = [];
           foreach($mutators as $mutator){
               foreach($mutator::nodesToHandle() as $node){
                   $map[$node][] = $mutator;
               }
           }

           return self::$map = $map;
    }
}
