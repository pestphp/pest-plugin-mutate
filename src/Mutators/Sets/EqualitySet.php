<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Equality\EqualToIdentical;
use Pest\Mutate\Mutators\Equality\GreaterOrEqualToGreater;
use Pest\Mutate\Mutators\Equality\GreaterToGreaterOrEqual;
use Pest\Mutate\Mutators\Equality\IdenticalToEqual;
use Pest\Mutate\Mutators\Equality\NotEqualToNotIdentical;
use Pest\Mutate\Mutators\Equality\NotIdenticalToNotEqual;
use Pest\Mutate\Mutators\Equality\SmallerOrEqualToSmaller;
use Pest\Mutate\Mutators\Equality\SmallerToSmallerOrEqual;
use Pest\Mutate\Mutators\Equality\SpaceshipSwitchSides;

class EqualitySet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            ...self::defaultMutators(),
            IdenticalToEqual::class,
            NotIdenticalToNotEqual::class,
        ];
    }

    /**
     * @return array<int, class-string<Mutator>>
     */
    public static function defaultMutators(): array
    {
        return [
            GreaterToGreaterOrEqual::class,
            GreaterOrEqualToGreater::class,
            SmallerToSmallerOrEqual::class,
            SmallerOrEqualToSmaller::class,
            EqualToIdentical::class,
            NotEqualToNotIdentical::class,
            SpaceshipSwitchSides::class,
        ];
    }
}
