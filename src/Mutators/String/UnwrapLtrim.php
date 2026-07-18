<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapLtrim extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'String';

    public const string DESCRIPTION = 'Unwraps `ltrim` calls.';

    public const string DIFF = <<<'DIFF'
        $a = ltrim(' Hello World');  // [tl! remove]
        $a = ' Hello World';  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'ltrim';
    }
}
