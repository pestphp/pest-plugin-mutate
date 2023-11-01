<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapStripTags extends AbstractFunctionCallUnwrapMutator
{
    public static function functionName(): string
    {
        return 'strip_tags';
    }
}
