<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapStrPad extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'String';

    public const string DESCRIPTION = 'Unwraps `str_pad` calls.';

    public const string DIFF = <<<'DIFF'
        $a = str_pad('Hello World', 20, '-');  // [tl! remove]
        $a = 'Hello World';  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'str_pad';
    }
}
