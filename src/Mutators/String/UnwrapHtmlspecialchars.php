<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapHtmlspecialchars extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'String';

    public const string DESCRIPTION = 'Unwraps `htmlspecialchars` calls.';

    public const string DIFF = <<<'DIFF'
        $a = htmlspecialchars('<h1>Hello World</h1>');  // [tl! remove]
        $a = '<h1>Hello World</h1>';  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'htmlspecialchars';
    }
}
