<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapHtmlspecialcharsDecode extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'String';

    public const string DESCRIPTION = 'Unwraps `htmlspecialchars_decode` calls.';

    public const string DIFF = <<<'DIFF'
        $a = htmlspecialchars_decode('&lt;h1&gt;Hello World&lt;/h1&gt;');  // [tl! remove]
        $a = '&lt;h1&gt;Hello World&lt;/h1&gt;';  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'htmlspecialchars_decode';
    }
}
