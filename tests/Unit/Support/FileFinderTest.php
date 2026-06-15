<?php

declare(strict_types=1);

use Pest\Mutate\Support\FileFinder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Invoke the private cross-platform absolute-path classifier.
 *
 * Reflection keeps the helper private (no public API change) while letting the
 * Linux CI runner verify the Windows drive-letter cases it cannot exercise
 * through real directories. Matches the reflection usage already in tests/Arch.php.
 */
function isAbsolutePath(string $path): bool
{
    return (bool) (new ReflectionMethod(FileFinder::class, 'isAbsolutePath'))->invoke(null, $path);
}

describe('isAbsolutePath()', function (): void {
    it('treats leading-separator paths as absolute', function (string $path): void {
        expect(isAbsolutePath($path))->toBeTrue();
    })->with([
        'POSIX root' => '/var/www/app',
        'POSIX short' => '/app',
        'UNC share' => '\\\\server\\share',
        'leading backslash' => '\\app',
    ]);

    it('treats Windows drive-letter paths as absolute', function (string $path): void {
        expect(isAbsolutePath($path))->toBeTrue();
    })->with([
        'drive + backslash' => 'C:\\project\\app', // the path PHPUnit hands the plugin on Windows
        'drive + forward slash' => 'C:/project/app',
        'lower-case drive' => 'd:\\app',
    ]);

    it('treats relative paths as not absolute', function (string $path): void {
        expect(isAbsolutePath($path))->toBeFalse();
    })->with([
        'bare directory' => 'app',
        'nested' => 'tests/Unit',
        'src' => 'src',
        'drive-relative (no separator)' => 'C:app',
        'empty' => '',
    ]);
});

it('finds php files when given an absolute source directory', function (): void {
    // Regression for the Windows bug: PHPUnit supplies an ABSOLUTE <source>
    // directory; FileFinder must not prepend getcwd() to it (which doubled the
    // path and dropped the directory, yielding "0 Mutations for 0 Files created").
    $absolute = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'Classes';

    expect($absolute)->toBeDirectory();

    $names = array_map(
        fn (SplFileInfo $file): string => $file->getFilename(),
        iterator_to_array(FileFinder::files([$absolute], [])),
    );

    expect($names)->toContain('AgeHelper.php', 'SizeHelper.php');
});
