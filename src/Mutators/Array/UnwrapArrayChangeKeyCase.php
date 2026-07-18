<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Array;

use Pest\Mutate\Mutators\Abstract\AbstractFunctionCallUnwrapMutator;

class UnwrapArrayChangeKeyCase extends AbstractFunctionCallUnwrapMutator
{
    public const string SET = 'Array';

    public const string DESCRIPTION = 'Unwraps `array_change_key_case` calls.';

    public const string DIFF = <<<'DIFF'
        $a = array_change_key_case(['foo' => 'bar'], CASE_UPPER);  // [tl! remove]
        $a = ['foo' => 'bar'];  // [tl! add]
        DIFF;

    public static function functionName(): string
    {
        return 'array_change_key_case';
    }
}
