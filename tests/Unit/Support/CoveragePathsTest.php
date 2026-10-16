<?php

declare(strict_types=1);

use Pest\Mutate\Support\CoveragePaths;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\Util\PathReducer;

beforeEach(function (): void {
    $this->restore = function (array $files): array {
        $coverageData = new ProcessedCodeCoverageData;
        $coverageData->setLineCoverage(array_fill_keys($files, [1 => [0 => 1]]));

        // reduces the paths the same way `--coverage-php` does before writing the report
        $basePath = new PathReducer()->reduce($coverageData);

        CoveragePaths::restore($coverageData, $basePath);

        return $coverageData->coveredFiles();
    };
});

it('restores the absolute paths of the covered files', function (array $files): void {
    expect(($this->restore)($files))->toEqualCanonicalizing($files);
})->with([
    'single file' => [[
        DIRECTORY_SEPARATOR.'project'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'A.php',
    ]],
    'files sharing a directory' => [[
        DIRECTORY_SEPARATOR.'project'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'A.php',
        DIRECTORY_SEPARATOR.'project'.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'B.php',
    ]],
    'files sharing only the root directory' => [[
        DIRECTORY_SEPARATOR.'project'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'A.php',
        DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'B.php',
    ]],
]);

it('restores the absolute paths of the covered files on windows', function (array $files): void {
    expect(($this->restore)($files))->toEqualCanonicalizing($files);
})->with([
    'files sharing only the drive' => [[
        'C:\\project\\src\\A.php',
        'C:\\tmp\\B.php',
    ]],
    'files on different drives' => [[
        'C:\\project\\src\\A.php',
        'D:\\tmp\\B.php',
    ]],
])->onlyOnWindows();
