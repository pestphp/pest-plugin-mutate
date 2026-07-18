<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Math;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class CeilToFloor extends AbstractFunctionReplaceMutator
{
    public const string SET = 'Math';

    public const string DESCRIPTION = 'Replaces `ceil` function with `floor` function.';

    public const string DIFF = <<<'DIFF'
        $a = ceil(1.2);  // [tl! remove]
        $a = floor(1.2);  // [tl! add]
        DIFF;

    public static function from(): string
    {
        return 'ceil';
    }

    public static function to(): string
    {
        return 'floor';
    }
}
