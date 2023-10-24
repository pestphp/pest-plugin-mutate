<?php

declare(strict_types=1);

namespace Pest\Mutate\Options;

use Symfony\Component\Console\Input\InputOption;

class MinMsiOption
{
    final public const ARGUMENT = 'min';

    public static function remove(): bool
    {
        return false;
    }

    public static function match(string $argument): bool
    {
        return str_starts_with($argument, sprintf('--%s=', self::ARGUMENT));
    }

    public static function inputOption(): InputOption
    {
        return new InputOption('--min', null, InputOption::VALUE_REQUIRED, '', 0);
    }
}