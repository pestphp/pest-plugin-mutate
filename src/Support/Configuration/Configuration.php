<?php

declare(strict_types=1);

namespace Pest\Mutate\Support\Configuration;

use Pest\Mutate\Contracts\Mutator;

class Configuration
{
    /**
     * @param  array<int, string>  $paths
     * @param  array<int, string>  $pathsToIgnore
     * @param  array<int, class-string<Mutator>>  $mutators
     * @param  array<int, string>  $classes
     */
    public function __construct(
        public readonly bool $coveredOnly,
        public readonly array $paths,
        public readonly array $pathsToIgnore,
        public readonly array $mutators,
        public readonly array $classes,
        public readonly bool $parallel,
        public readonly int $processes,
        public readonly bool $profile,
        public readonly ?float $minScore,
        public readonly bool $ignoreMinScoreOnZeroMutations,
        public readonly bool $stopOnEscaped,
        public readonly bool $stopOnNotCovered,
        public readonly bool $uncommittedOnly,
        public readonly string|false $changedOnly,
        public readonly ?string $mutationId,
        public readonly bool $retry,
    ) {}
}
