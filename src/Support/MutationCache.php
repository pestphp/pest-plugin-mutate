<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use Pest\Mutate\Mutation;
use Symfony\Component\Finder\SplFileInfo;

class MutationCache
{
    private static ?self $instance = null;

    /**
     * @var array<string, array<int, Mutation>>
     */
    private array $cache = [];

    public static function instance(): self
    {
        return self::$instance ?? self::$instance = new self();
    }

    public function __construct()
    {
        $this->restore();
    }

    public function has(SplFileInfo $file, string $mutator): bool
    {
        return array_key_exists(self::getKey($file, $mutator), $this->cache);
    }

    /**
     * @return array<int, Mutation>
     */
    public function get(SplFileInfo $file, string $mutator): array
    {
        return $this->cache[self::getKey($file, $mutator)];
    }

    /**
     * @param  array<int, Mutation>  $mutations
     */
    public function put(SplFileInfo $file, string $mutator, array $mutations): void
    {
        $this->cache[self::getKey($file, $mutator)] = $mutations;
    }

    private function getKey(SplFileInfo $file, string $mutator): string
    {
        return $file->getRealPath().'::'.$mutator;
    }

    public function persist(): void
    {
        $file = $this->getCacheFile();

        file_put_contents($file, serialize($this->cache));
    }

    private function restore(): void
    {
        $file = $this->getCacheFile();

        if (! file_exists($file)) {
            return;
        }

        $this->cache = unserialize(file_get_contents($file)); // @phpstan-ignore-line
    }

    private function getCacheFile(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            dirname(__DIR__, 2),
            '.temp',
            'mutation-cache.bin',
        ]);
    }
}
