<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\String;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionReplaceMutator;

class StrStartsWithToStrEndsWith extends AbstractFunctionReplaceMutator
{
    public static function from(): string
    {
        return 'str_starts_with';
    }

    public static function to(): string
    {
        return 'str_ends_with';
    }
}
