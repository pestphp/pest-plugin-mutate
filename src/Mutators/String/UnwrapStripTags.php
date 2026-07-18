<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapStripTags extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'String';

    public const string DESCRIPTION = 'Unwraps `strip_tags` calls.';

    public const string DIFF = <<<'DIFF'
        $a = strip_tags('Hello World');  // [tl! remove]
        $a = 'Hello World';  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'strip_tags';
    }
}
