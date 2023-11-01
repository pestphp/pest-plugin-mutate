<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapHtmlEntityDecode extends AbstractFunctionCallUnwrapMutator
{
    public static function functionName(): string
    {
        return 'html_entity_decode';
    }
}
