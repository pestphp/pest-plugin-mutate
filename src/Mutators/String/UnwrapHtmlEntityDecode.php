<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapHtmlEntityDecode extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'String';

    public const string DESCRIPTION = 'Unwraps `html_entity_decode` calls.';

    public const string DIFF = <<<'DIFF'
        $a = html_entity_decode('&lt;h1&gt;Hello World&lt;/h1&gt;');  // [tl! remove]
        $a = '&lt;h1&gt;Hello World&lt;/h1&gt;';  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'html_entity_decode';
    }
}
