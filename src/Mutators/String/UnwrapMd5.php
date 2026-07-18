<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapMd5 extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'String';

    public const string DESCRIPTION = 'Unwraps `md5` calls.';

    public const string DIFF = <<<'DIFF'
        $a = md5('Hello World');  // [tl! remove]
        $a = 'Hello World';  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'md5';
    }
}
