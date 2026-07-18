<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapArrayIntersect extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'Array';

    public const string DESCRIPTION = 'Unwraps `array_intersect` calls.';

    public const string DIFF = <<<'DIFF'
        $a = array_intersect([1, 2, 3], [1, 2]);  // [tl! remove]
        $a = [1, 2, 3];  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'array_intersect';
    }
}
