<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;

class CoveragePaths
{
    /**
     * Restores the absolute paths of the covered files: since phpunit/php-code-coverage 14,
     * `--coverage-php` writes the file keys relative to `basePath`.
     */
    public static function restore(ProcessedCodeCoverageData $coverageData, string $basePath): void
    {
        foreach ($coverageData->coveredFiles() as $relativePath) {
            $coverageData->renameFile($relativePath, self::absolute($relativePath, $basePath));
        }
    }

    /**
     * @param  non-empty-string  $relativePath
     * @return non-empty-string
     */
    private static function absolute(string $relativePath, string $basePath): string
    {
        if ($basePath !== '') {
            return $basePath.DIRECTORY_SEPARATOR.$relativePath;
        }

        // the base path is also empty when the covered files share only the root directory,
        // and their keys then lost the leading separator; files without a common ancestor at
        // all (e.g. on different Windows drives) keep their absolute paths
        if (preg_match('/^[A-Za-z]:/', $relativePath) === 1) {
            return $relativePath;
        }

        return DIRECTORY_SEPARATOR.$relativePath;
    }
}
