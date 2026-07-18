<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapArrayKeys extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'Array';

    public const string DESCRIPTION = 'Unwraps `array_keys` calls.';

    public const string DIFF = <<<'DIFF'
        $a = array_keys(['foo' => 1, 'bar' => 2]);  // [tl! remove]
        $a = ['foo' => 1, 'bar' => 2];  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'array_keys';
    }
}
