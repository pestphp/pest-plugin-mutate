<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapArrayUintersectAssoc extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'Array';

    public const string DESCRIPTION = 'Unwraps `array_uintersect_assoc` calls.';

    public const string DIFF = <<<'DIFF'
        $a = array_uintersect_assoc([1, 2, 3], [1, 2, 4], fn ($a, $b) => $a <=> $b);  // [tl! remove]
        $a = [1, 2, 3];  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'array_uintersect_assoc';
    }
}
