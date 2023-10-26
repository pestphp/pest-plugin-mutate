<?php

declare(strict_types=1);

namespace Pest\Mutate\Options;

use Symfony\Component\Console\Input\InputOption;

class ParallelOption
{
    final public const ARGUMENT = 'parallel';

    public static function remove(): bool
    {
        return false;
    }

    public static function match(string $argument): bool
    {
        return $argument === sprintf('--%s', self::ARGUMENT);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption('--parallel', null, InputOption::VALUE_NONE, '');
    }
}
