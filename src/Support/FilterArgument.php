<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use RuntimeException;

/**
 * The single `--filter=` argv element handed to a mutant's child process.
 *
 * One fragment per covering test, joined with `|`, is correct and unbounded — and on a
 * class that most of a suite reaches it runs past the kernel's cap on ONE argv element.
 * Linux caps it at MAX_ARG_STRLEN = PAGE_SIZE * 32 (include/uapi/linux/binfmts.h), which
 * is 131072 bytes on x86_64 and is not tunable: `ulimit -s` moves the total ARG_MAX, never
 * the per-element cap. The child then dies with `posix_spawn() failed: Argument list too
 * long` before it runs a single test, so every mutation in that class is lost.
 *
 * macOS has no per-argument cap, which is why this reproduces only on Linux — typically in
 * CI, on the run whose numbers people actually publish.
 *
 * Two encodings, applied in that order:
 *
 *  1. FACTOR the class prefix. `Cls::(.*)a|Cls::(.*)b` becomes `Cls::(.*)(a|b)`. Lossless:
 *     both forms select exactly the same tests, and it is about 28% shorter on a real
 *     suite. The inner parentheses are load-bearing — without them the alternation binds
 *     to the whole pattern instead of the tail, and the filter selects far more than it
 *     was asked for.
 *
 *  2. If it still does not fit, COLLAPSE a class to its bare `Cls::` prefix, largest
 *     contributor first, stopping the moment it fits. That is a strict SUPERSET of the
 *     original selection, which is exactly why it is safe here: widening can only run MORE
 *     tests, so it can never turn a killed mutant into a survivor.
 *
 * That safety argument has one precondition and it is not decorative: it holds only while
 * the ordinary suite is green. A test already failing for unrelated reasons counts as a
 * kill, so a widened filter can inherit an unrelated red — the same fabrication class as
 * running mutation in parallel.
 *
 * A widening is therefore never silent: `widened` names every class and how many extra
 * tests it now selects, so the caller can say so. A filter that quietly widened would make
 * the score certify more than the run measured.
 *
 * @see https://github.com/pestphp/pest/issues/1771
 */
final readonly class FilterArgument
{
    /**
     * Byte budget for the single `--filter=` argv element.
     *
     * 96 KiB rather than the kernel's full 131072: the cap is per element and a suite only
     * grows, so a filter sized to the exact limit is one new test away from failing again.
     */
    public const int BUDGET_BYTES = 98304;

    /**
     * @param  array<string, int>  $widened  class => how many extra tests it now selects
     */
    private function __construct(
        public string $argument,
        public array $widened,
    ) {}

    /**
     * @param  list<string>  $filters  one regex fragment per covering test
     *
     * @throws RuntimeException when even a fully collapsed filter does not fit
     */
    public static function for(array $filters, int $budget = self::BUDGET_BYTES): self
    {
        /** @var array<string, list<string>> $byClass */
        $byClass = [];

        foreach ($filters as $filter) {
            // A fragment whose shape is not recognised is kept verbatim under a reserved
            // key. If the shape upstream builds ever changes, this degrades to today's
            // behaviour rather than silently dropping a selection.
            if (preg_match('/^([A-Za-z0-9_]+)::\(\.\*\)(.*)$/s', $filter, $matches) === 1) {
                $byClass[$matches[1]][] = $matches[2];

                continue;
            }

            $byClass["\0raw"][] = $filter;
        }

        /** @var array<string, true> $collapsed */
        $collapsed = [];

        $render = static function () use (&$byClass, &$collapsed): string {
            $parts = [];

            /**
             * @var string $class
             * @var list<string> $tails
             */
            foreach ($byClass as $class => $tails) {
                if ($class === "\0raw") {
                    foreach ($tails as $tail) {
                        $parts[] = $tail;
                    }

                    continue;
                }

                if (isset($collapsed[$class])) {
                    $parts[] = $class.'::';

                    continue;
                }

                $parts[] = $class.'::(.*)('.implode('|', array_unique($tails)).')';
            }

            return '--filter="'.implode('|', $parts).'"';
        };

        $argument = $render();

        if (strlen($argument) <= $budget) {
            return new self($argument, []);
        }

        // Heaviest class first, so the fewest collapses buy the most room.
        /** @var array<string, int> $weights */
        $weights = [];

        foreach ($byClass as $class => $tails) {
            if ($class !== "\0raw") {
                $weights[$class] = strlen(implode('|', array_unique($tails)));
            }
        }

        arsort($weights);

        /** @var array<string, int> $widened */
        $widened = [];

        foreach (array_keys($weights) as $class) {
            $collapsed[$class] = true;
            $widened[$class] = count(array_unique($byClass[$class]));
            $argument = $render();

            if (strlen($argument) <= $budget) {
                break;
            }
        }

        if (strlen($argument) > $budget) {
            // Dropping the filter entirely would run the whole suite per mutant and read
            // as a fast green, so this fails loudly instead.
            throw new RuntimeException(sprintf(
                'The mutation filter is %d bytes with every class collapsed to its prefix, over the %d-byte budget.',
                strlen($argument),
                $budget,
            ));
        }

        return new self($argument, $widened);
    }

    /**
     * A human-readable note for a run whose filter had to widen, or null when none did.
     */
    public function notice(): ?string
    {
        if ($this->widened === []) {
            return null;
        }

        return sprintf(
            'Mutation filter collapsed %d class(es) to a bare prefix to fit within %d bytes: %s. '.
            'This selects a superset of the covering tests, so it cannot fabricate a kill — '.
            'provided the ordinary suite is green before mutation starts.',
            count($this->widened),
            strlen($this->argument),
            implode(', ', array_map(
                static fn (string $class, int $count): string => $class.' (+'.$count.' tests)',
                array_keys($this->widened),
                array_values($this->widened),
            )),
        );
    }
}
