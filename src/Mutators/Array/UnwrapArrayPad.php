<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapArrayPad extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'Array';

    public const string DESCRIPTION = 'Unwraps `array_pad` calls.';

    public const string DIFF = <<<'DIFF'
        $a = array_pad([1, 2, 3], 5, 0);  // [tl! remove]
        $a = [1, 2, 3];  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'array_pad';
    }
}
