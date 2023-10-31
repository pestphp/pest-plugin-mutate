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
            ...AssignmentSet::mutators(),
            ...ControlStructuresSet::mutators(),
            ...EqualitySet::defaultMutators(),
            ...LogicalSet::mutators(),
            ...MathSet::mutators(),
        ];
    }
}
