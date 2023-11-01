<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\MutatorSet;

class DefaultSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            ...ArithmeticSet::mutators(),
            ...ArraySet::mutators(),
            ...AssignmentSet::mutators(),
            ...CastingSet::mutators(),
            ...ControlStructuresSet::mutators(),
            ...EqualitySet::defaultMutators(),
            ...LogicalSet::mutators(),
            ...MathSet::mutators(),
            ...NumberSet::mutators(),
            ...StringSet::mutators(),
        ];
    }
}
