<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class ArrayShiftToArrayPop extends AbstractFunctionReplaceMutator
{
    public const string SET = 'Array';

    public const string DESCRIPTION = 'Replaces `array_shift` with `array_pop`.';

    public const string DIFF = <<<'DIFF'
        $a = array_shift([1, 2, 3]);  // [tl! remove]
        $a = array_pop([1, 2, 3]);  // [tl! add]
        DIFF;

    public static function from(): string
    {
        return 'array_shift';
    }

    public static function to(): string
    {
        return 'array_pop';
    }
}
