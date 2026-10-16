<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;
use Tests\Fixtures\CoveragePaths\Generated\Threshold;

beforeEach(function (): void {
    $root = dirname(__DIR__, 2);

    // outside the repository: on Linux and macOS, the covered files then share only the root
    // directory, and php-code-coverage reports them without their leading separator
    $this->directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pest-mutate-coverage-paths';

    if (! is_dir($this->directory)) {
        mkdir($this->directory);
    }

    file_put_contents($this->directory.DIRECTORY_SEPARATOR.'Threshold.php', <<<'PHP'
        <?php

        namespace Tests\Fixtures\CoveragePaths\Generated;

        class Threshold
        {
            public static function reached(int $value): bool
            {
                return $value >= 10;
            }
        }
        PHP);

    file_put_contents($this->directory.DIRECTORY_SEPARATOR.'phpunit.xml', <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <phpunit bootstrap="{$root}/vendor/autoload.php" cacheDirectory="{$root}/.phpunit.cache" colors="true">
            <testsuites>
                <testsuite name="coverage-paths">
                    <directory suffix="Test.php">{$root}/tests/.tests/CoveragePaths</directory>
                </testsuite>
            </testsuites>
            <source>
                <include>
                    <directory suffix=".php">{$root}/tests/Fixtures/Classes</directory>
                    <directory suffix=".php">{$this->directory}</directory>
                </include>
            </source>
        </phpunit>
        XML);
});

it('mutates the covered files that share only the root directory', function (array $arguments): void {
    $process = new Process(
        [
            PHP_BINARY,
            'vendor/bin/pest',
            '--configuration='.$this->directory.DIRECTORY_SEPARATOR.'phpunit.xml',
            'tests/.tests/CoveragePaths',
            '--mutate',
            '--mutator=EqualityGreaterOrEqualToGreater',
            // mutates() would make Pest write a lock file shared with this test suite
            '--class='.Threshold::class,
            '--colors=never',
            ...$arguments,
        ],
        dirname(__DIR__, 2),
        [
            'COLUMNS' => '120',
            'XDEBUG_MODE' => 'coverage',
            'PEST_PLUGIN_INTERNAL_TEST_SUITE' => '1',
            'PEST_MUTATE_GENERATED_COVERAGE_PATHS' => $this->directory,
            // when this test suite runs in parallel, the mutation tests must not run as a worker
            'PARATEST' => false,
        ],
        timeout: 120,
    );

    $process->run();

    $output = $process->getOutput().$process->getErrorOutput();

    expect(preg_match('/Mutations: *(.+)/', $output, $matches))->toBe(1, $output)
        ->and(trim($matches[1] ?? ''))->toBe('1 tested', $output);
})->with([
    'all the mutations' => [[]],
    'covered mutations only' => [['--covered-only']],
])->skipOnWindows(); // the file finder takes the paths with a drive letter for relative paths
