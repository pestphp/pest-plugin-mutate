<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use DateInterval;
use DateTime;
use Exception;
use Psr\SimpleCache\CacheInterface;

class FileCache implements CacheInterface
{
    /**
     * The temporary folder.
     */
    private const CACHE_FOLDER = __DIR__
    .DIRECTORY_SEPARATOR
    .'..'
    .DIRECTORY_SEPARATOR
    .'..'
    .DIRECTORY_SEPARATOR
    .'.temp'
    .DIRECTORY_SEPARATOR
    .'cache';

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getPayload($key)['data'] ?? $default;
    }

    public function set(string $key, mixed $value, DateInterval|int $ttl = null): bool
    {
        $payload = serialize($value);

        $expire = $this->expiration($ttl);

        $content = $expire.$payload;

        $result = file_put_contents($this->filePathFromKey($key), $content);

        return $result !== false;
    }

    public function delete(string $key): bool
    {
        return unlink($this->filePathFromKey($key));
    }

    public function clear(): bool
    {
        array_map('unlink', array_filter((array) glob(self::CACHE_FOLDER.DIRECTORY_SEPARATOR.'*')));

        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * @param  iterable<string, mixed>  $values
     */
    public function setMultiple(iterable $values, DateInterval|int $ttl = null): bool // @phpstan-ignore-line
    {
        $result = true;

        foreach ($values as $key => $value) {
            if (! $this->set($key, $value, $ttl)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param  iterable<string>  $keys
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $result = true;

        foreach ($keys as $key) {
            if (! $this->delete($key)) {
                $result = false;
            }
        }

        return $result;
    }

    public function has(string $key): bool
    {
        return file_exists($this->filePathFromKey($key));
    }

    private function filePathFromKey(string $key): string
    {
        return self::CACHE_FOLDER.DIRECTORY_SEPARATOR.hash('xxh3', $key);
    }

    private function expiration(DateInterval|int|null $seconds): int
    {
        if ($seconds === null) {
            return 9_999_999_999;
        }
        if ($seconds === 0) {
            return 9_999_999_999;
        }
        if ($seconds instanceof DateInterval) {
            return (new DateTime())->add($seconds)->getTimestamp();
        }

        return time() + $seconds;
    }

    /**
     * @return array{data: mixed, time: int|null}
     */
    private function getPayload(string $key): array
    {
        if (! file_exists($this->filePathFromKey($key))) {
            return $this->emptyPayload();
        }

        $content = file_get_contents($this->filePathFromKey($key));

        if ($content === false) {
            return $this->emptyPayload();
        }

        try {
            $expire = (int) substr(
                $content, 0, 10
            );
        } catch (Exception) { // @phpstan-ignore-line
            $this->delete($key);

            return $this->emptyPayload();
        }

        if (time() >= $expire) {
            $this->delete($key);

            return $this->emptyPayload();
        }

        try {
            $data = unserialize(substr($content, 10));
        } catch (Exception) { // @phpstan-ignore-line
            $this->delete($key);

            return $this->emptyPayload();
        }

        $time = $expire - time();

        return ['data' => $data, 'time' => $time];
    }

    /**
     * @return array{data: null, time: null}
     */
    protected function emptyPayload(): array
    {
        return ['data' => null, 'time' => null];
    }
}
