<?php

declare(strict_types=1);

use Pest\Mutate\Exceptions\InvalidMutatorException;
use Pest\Mutate\Factories\ProfileFactory;

it('throws an exception if an unknown mutator class name is passed', function (): void {
    (new ProfileFactory('default'))
        ->mutator('UnknownMutatorClass');
})->throws(InvalidMutatorException::class, 'UnknownMutatorClass is not a valid mutator');
