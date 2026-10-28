<?php

declare(strict_types=1);

use Pest\Exceptions\InvalidOption;
use Pest\Mutate\Support\ChangedScope;
use Pest\Mutate\Support\FileFinder;
use Symfony\Component\Process\Process;

beforeEach(function (): void {
    $this->namespace = 'App\\Changed'.bin2hex(random_bytes(4));
    $this->root = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pest-mutate-changed-'.bin2hex(random_bytes(4));

    mkdir($this->root);
    $this->root = (string) realpath($this->root);

    $this->write = function (array $files): void {
        foreach ($files as $path => $code) {
            is_dir(dirname($this->root.'/'.$path)) || mkdir(dirname($this->root.'/'.$path), recursive: true);
            file_put_contents($this->root.'/'.$path, str_replace('App\\Ns', $this->namespace, (string) $code));
        }
    };

    $this->git = function (string ...$arguments): string {
        $process = new Process(['git', '-c', 'user.name=Pest', '-c', 'user.email=pest@example.com', '-c', 'commit.gpgsign=false', '-c', 'core.hooksPath=/dev/null', ...$arguments], $this->root);
        $process->mustRun();

        return $process->getOutput();
    };

    $this->commit = function (array $files): void {
        ($this->write)($files);
        ($this->git)('add', '--all');
        ($this->git)('commit', '-q', '-m', 'commit');
    };

    $this->scope = function (string $revision = 'HEAD', array $coveredLines = []): ChangedScope {
        foreach (glob($this->root.'/src/*.php') ?: [] as $file) {
            require_once $file;
        }

        return ChangedScope::since(
            revision: $revision,
            root: $this->root,
            sourceFiles: FileFinder::files([$this->root.'/src'], []),
            sourcePaths: [$this->root.'/src'],
            testPath: 'tests',
            testFiles: glob($this->root.'/tests/*Test.php') ?: [],
            coveredLines: $coveredLines,
        );
    };

    $this->files = function (ChangedScope $scope): array {
        $files = array_map(fn (string $file): string => substr($file, strlen($this->root) + 1), $scope->files());
        sort($files);

        return $files;
    };

    ($this->git)('init', '-q', '-b', 'main');
    ($this->commit)([
        'README.md' => '# Project',
        'src/Foo.php' => '<?php namespace App\Ns; class Foo {}',
        'src/Bar.php' => '<?php namespace App\Ns; class Bar {}',
        'src/Sub/Baz.php' => '<?php namespace App\Ns\Sub; class Baz {}',
        'tests/Pest.php' => '<?php',
        'tests/FooTest.php' => '<?php use App\Ns\Foo; use App\Ns\Bar; covers(Foo::class, Bar::class); it("works", fn () => true);',
    ]);
});

afterEach(function (): void {
    (new Process(['rm', '-rf', $this->root]))->run();
});

it('only scopes the source files that changed', function (): void {
    ($this->write)(['src/Foo.php' => '<?php namespace App\Ns; class Foo { public $changed; }']);

    $scope = ($this->scope)();

    expect($scope->everythingBecause)->toBeNull()
        ->and(($this->files)($scope))->toBe(['src/Foo.php']);
});

it('scopes new source files that are not tracked yet', function (): void {
    ($this->write)(['src/Qux.php' => '<?php namespace App\Ns; class Qux {}']);

    expect(($this->files)(($this->scope)()))->toBe(['src/Qux.php']);
});

it('scopes nothing when only files without mutants changed', function (): void {
    ($this->write)(['README.md' => '# Changed']);

    $scope = ($this->scope)();

    expect($scope->everythingBecause)->toBeNull()
        ->and(($this->files)($scope))->toBe([]);
});

it('scopes what a changed test file covers before and after the change', function (): void {
    ($this->write)(['tests/FooTest.php' => '<?php use App\Ns\Foo; covers(Foo::class); it("works", fn () => true);']);

    expect(($this->files)(($this->scope)()))->toBe(['src/Bar.php', 'src/Foo.php']);
});

it('scopes what a deleted test file covered', function (): void {
    unlink($this->root.'/tests/FooTest.php');

    expect(($this->files)(($this->scope)()))->toBe(['src/Bar.php', 'src/Foo.php']);
});

it('scopes the namespaces a changed test file mutates', function (): void {
    ($this->write)(['tests/SubTest.php' => '<?php mutates("App\Ns\Sub"); it("works", fn () => true);']);

    expect(($this->files)(($this->scope)()))->toBe(['src/Sub/Baz.php']);
});

it('scopes the source files the tests of a changed test file run', function (): void {
    ($this->write)(['tests/FooTest.php' => '<?php use App\Ns\Foo; covers(Foo::class); it("works", fn () => true);']);

    $testCase = 'P\\Tests\\'.str_replace('\\', '', $this->namespace).'FooTest';
    eval(sprintf('namespace %s; final class %s { public static $__filename = %s; }', substr($testCase, 0, (int) strrpos($testCase, '\\')), substr($testCase, (int) strrpos($testCase, '\\') + 1), var_export($this->root.'/tests/FooTest.php', true)));

    $scope = ($this->scope)('HEAD', [$this->root.'/src/Sub/Baz.php' => [1 => [$testCase.'::__pest_evaluable_it_works']]]);

    expect(($this->files)($scope))->toBe(['src/Bar.php', 'src/Foo.php', 'src/Sub/Baz.php']);
});

it('compares against where HEAD forked from the given revision', function (): void {
    ($this->git)('switch', '-q', '-c', 'feature');
    ($this->commit)(['src/Foo.php' => '<?php namespace App\Ns; class Foo { public $changed; }']);
    ($this->git)('switch', '-q', 'main');
    ($this->commit)(['src/Bar.php' => '<?php namespace App\Ns; class Bar { public $changed; }']);
    ($this->git)('switch', '-q', 'feature');

    expect(($this->files)(($this->scope)('main')))->toBe(['src/Foo.php']);
});

it('scopes nothing when a support file only gained functions', function (): void {
    ($this->write)(['tests/Pest.php' => '<?php use App\Ns\Foo; function foo(): Foo { return new Foo; }']);

    $scope = ($this->scope)();

    expect($scope->everythingBecause)->toBeNull()
        ->and(($this->files)($scope))->toBe([]);
});

it('mutates everything when a non-test file in the tests path changed', function (): void {
    ($this->write)(['tests/Pest.php' => '<?php pest()->extend(Tests\TestCase::class);']);

    expect(($this->scope)()->everythingBecause)->toBe('tests/Pest.php changed');
});

it('mutates everything when the dependencies changed', function (): void {
    ($this->write)(['composer.lock' => '{}']);

    expect(($this->scope)()->everythingBecause)->toBe('composer.lock changed');
});

it('mutates everything when a changed test file covers something other than a name', function (): void {
    ($this->write)(['tests/FooTest.php' => '<?php covers($class); it("works", fn () => true);']);

    expect(($this->scope)()->everythingBecause)->toBe('tests/FooTest.php covers something other than class or namespace names');
});

it('fails when the revision shares no history with HEAD', function (): void {
    ($this->scope)('does-not-exist');
})->throws(InvalidOption::class, 'Mutating changes requires git and a common ancestor of [does-not-exist] and HEAD.');

it('mutates everything when a support file changed what it already declared', function (): void {
    ($this->commit)(['tests/Pest.php' => '<?php function foo(): int { return 1; }']);
    ($this->write)(['tests/Pest.php' => '<?php function foo(): int { return 2; } function bar(): int { return 3; }']);

    expect(($this->scope)()->everythingBecause)->toBe('tests/Pest.php changed');
});

it('mutates everything when a non-php file in the tests path changed', function (): void {
    ($this->commit)(['tests/Fixtures/answer.json' => '{}']);
    ($this->write)(['tests/Fixtures/answer.json' => '{"changed": true}']);

    expect(($this->scope)()->everythingBecause)->toBe('tests/Fixtures/answer.json changed');
});
