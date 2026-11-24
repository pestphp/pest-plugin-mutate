<?php

declare(strict_types=1);

use Pest\Mutate\Cache\FileStore;
use Pest\Mutate\Mutators\Equality\GreaterOrEqualToGreater;
use Pest\Mutate\Support\MutationCache;
use Pest\Mutate\Support\MutationGenerator;
use Pest\Support\Container;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Finder\SplFileInfo;

beforeEach(function (): void {
    $this->generator = new MutationGenerator;
});

describe('with the mutation cache', function (): void {
    beforeEach(function (): void {
        $this->cacheDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pest-mutate-cache-'.bin2hex(random_bytes(6));
        $this->previousStore = Container::getInstance()->get(CacheInterface::class);
        $this->instance = new ReflectionProperty(MutationCache::class, 'instance');
        $this->previousInstance = $this->instance->getValue();

        Container::getInstance()->add(CacheInterface::class, new FileStore($this->cacheDirectory));
        $this->instance->setValue(null, null);

        $this->sourceFiles = [];

        $this->cachedFile = function (): SplFileInfo {
            $fileName = tempnam(sys_get_temp_dir(), 'pest-mutate');

            file_put_contents($fileName, <<<'PHP'
                <?php

                return $a >= $b;
                PHP);

            $this->sourceFiles[] = $fileName;

            return new SplFileInfo($fileName, '', '');
        };
    });

    afterEach(function (): void {
        Container::getInstance()->add(CacheInterface::class, $this->previousStore);
        $this->instance->setValue(null, $this->previousInstance);

        foreach (glob($this->cacheDirectory.DIRECTORY_SEPARATOR.'*') ?: [] as $file) {
            unlink($file);
        }

        rmdir($this->cacheDirectory);

        foreach ($this->sourceFiles as $sourceFile) {
            unlink($sourceFile);
        }
    });

    it('generates a cached mutation again when its modified source is gone', function (): void {
        $file = ($this->cachedFile)();

        $first = $this->generator->generate($file, [GreaterOrEqualToGreater::class]);

        expect($first)->toHaveCount(1);

        unlink($first[0]->modifiedSourcePath);

        $second = $this->generator->generate($file, [GreaterOrEqualToGreater::class]);

        expect($second)->toHaveCount(1)
            ->and($second[0]->modifiedSourcePath)->toBeFile();
    });

    it('reuses a cached mutation while its modified source exists', function (): void {
        $file = ($this->cachedFile)();

        $first = $this->generator->generate($file, [GreaterOrEqualToGreater::class]);

        touch($first[0]->modifiedSourcePath, 1_000_000_000);

        $this->generator->generate($file, [GreaterOrEqualToGreater::class]);

        clearstatcache();

        expect(filemtime($first[0]->modifiedSourcePath))->toBe(1_000_000_000);
    });
});
