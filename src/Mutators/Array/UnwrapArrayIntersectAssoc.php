<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapArrayIntersectAssoc extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'Array';

    public const string DESCRIPTION = 'Unwraps `array_intersect_assoc` calls.';

    public const string DIFF = <<<'DIFF'
        $a = array_intersect_assoc(['foo' => 1, 'bar' => 2], ['foo' => 1]);  // [tl! remove]
        $a = ['foo' => 1, 'bar' => 2];  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'array_intersect_assoc';
    }
}
