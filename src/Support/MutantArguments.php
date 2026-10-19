<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

/**
 * The arguments of the original run, reduced to what a mutant's subprocess can accept.
 *
 * A mutant runs one PHPUnit process, never ParaTest, so every ParaTest-only option has to
 * go. Left in, PHPUnit aborts with `Unknown option "--processes"` before running a single
 * test, and that exit code is indistinguishable from a killed mutant — so the mutant is
 * counted as tested and the score reads 100%.
 *
 * @see https://github.com/pestphp/pest/issues/1937
 */
final class MutantArguments
{
    /**
     * ParaTest's own options, mapped to whether they take a value.
     *
     * Only the options ParaTest adds on top of PHPUnit are listed: the ones it merely
     * forwards are valid for the mutant too.
     *
     * @var array<string, bool>
     */
    private const array PARATEST_ONLY = [
        '--functional' => false,
        '--max-batch-size' => true,
        '--max-processes' => true,
        '--no-test-tokens' => false,
        '--parallel' => false,
        '--passthru-php' => true,
        '--processes' => true,
        '--runner' => true,
        '--shard' => false,
        '--shard-test-distribution' => true,
        '--shard-test-distribution-seed' => true,
        '--tmp-dir' => true,
        '--verbose' => false,
        '-m' => true,
        // Pest uses -p as the parallel flag, so it must not swallow the next argument.
        // ParaTest's own -p is --processes, which Pest spells out as --processes.
        '-p' => false,
    ];

    /**
     * @param  array<int, string>  $originalArguments
     * @return array<int, string>
     */
    public static function from(array $originalArguments): array
    {
        $originalArguments = array_values($originalArguments);
        $arguments = [];
        $count = count($originalArguments);

        for ($index = 0; $index < $count; $index++) {
            $argument = $originalArguments[$index];

            // the mutant reuses the coverage the original run already collected
            if (str_starts_with($argument, '--coverage')) {
                if (self::valueFollows($originalArguments, $index, $argument)) {
                    $index++;
                }

                continue;
            }

            $name = explode('=', $argument, 2)[0];

            if (! array_key_exists($name, self::PARATEST_ONLY)) {
                $arguments[] = $argument;

                continue;
            }

            if ($name === '--shard') {
                $next = $originalArguments[$index + 1] ?? null;

                // `--shard 1/2` is the value. `--shard tests/Unit` is not: Pest rejects that form.
                if (! str_contains($argument, '=') && is_string($next) && preg_match('/^\d+\/\d+$/', $next) === 1) {
                    $index++;
                }

                continue;
            }

            // `--processes 2` spends two arguments, and a stray `2` would be read as a path
            if (self::PARATEST_ONLY[$name] && ! str_contains($argument, '=')) {
                $index++;
            }
        }

        return $arguments;
    }

    /**
     * `--coverage-php /tmp/coverage.php` spends two arguments. `--coverage` itself does not,
     * and neither does an option that already carries its value after `=`.
     *
     * @param  array<int, string>  $originalArguments
     */
    private static function valueFollows(array $originalArguments, int $index, string $argument): bool
    {
        if ($argument === '--coverage' || str_contains($argument, '=')) {
            return false;
        }

        $next = $originalArguments[$index + 1] ?? null;

        return is_string($next) && ! str_starts_with($next, '-');
    }
}
