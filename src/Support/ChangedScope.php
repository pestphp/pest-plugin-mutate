<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use Pest\Exceptions\InvalidOption;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

/**
 * The source files whose mutants a change since a git revision can affect.
 */
class ChangedScope
{
    /**
     * Files outside the source and test paths that every mutant depends on.
     */
    private const array PROJECT_FILES = ['composer.json', 'composer.lock', 'phpunit.xml', 'phpunit.xml.dist', 'phpunit.dist.xml'];

    private const array COVERING_CALLS = ['covers', 'coversclass', 'coverstrait', 'mutates'];

    /**
     * @var array<string, string|null>
     */
    private static array $testFilesByClass = [];

    /**
     * @param  array<string, mixed>  $files  keyed by real path
     */
    public function __construct(
        public readonly string $revision,
        public readonly string $base,
        public readonly ?string $everythingBecause,
        private readonly array $files = [],
    ) {}

    /**
     * @param  iterable<SplFileInfo>  $sourceFiles
     * @param  array<int, string>  $sourcePaths
     * @param  array<int, string>  $testFiles
     * @param  array<string, array<int, array<int, string>>>  $coveredLines
     */
    public static function since(
        string $revision,
        string $root,
        iterable $sourceFiles,
        array $sourcePaths,
        string $testPath,
        array $testFiles,
        array $coveredLines,
    ): self {
        $base = self::git($root, ['merge-base', $revision, 'HEAD'])
            ?? throw new InvalidOption("Mutating changes requires git and a common ancestor of [{$revision}] and HEAD. A shallow clone may need more history.");
        $base = trim($base);

        $changed = [
            ...self::split(self::git($root, ['diff', '--name-only', '--no-renames', '--relative', '-z', $base, '--']) ?? ''),
            ...self::split(self::git($root, ['ls-files', '--others', '--exclude-standard', '-z']) ?? ''),
        ];

        $sources = [];

        foreach ($sourceFiles as $file) {
            $sources[$file->getRealPath()] = $file;
        }

        $sourceRoots = array_map(fn (string $path): string => self::absolute($root, $path), $sourcePaths);
        $testRoot = self::absolute($root, $testPath);
        $testFiles = array_flip(array_map(fn (string $file): string => realpath($file) ?: $file, $testFiles));

        $files = [];
        $targets = [];
        $changedTests = [];

        foreach (array_unique($changed) as $path) {
            $absolute = $root.DIRECTORY_SEPARATOR.$path;
            $real = realpath($absolute) ?: $absolute;

            if (isset($sources[$real])) {
                $files[$real] = true;

                continue;
            }

            if (self::within($real, $sourceRoots)) {
                // A deleted or ignored source file has no mutants of its own.
                if (str_ends_with($path, '.php')) {
                    continue;
                }

                return new self($revision, $base, "{$path} changed");
            }

            if (self::within($real, [$testRoot])) {
                $isDeletedTest = ! file_exists($absolute) && str_ends_with($path, '.php');

                if (! isset($testFiles[$real]) && ! $isDeletedTest) {
                    if (self::onlyAddsFunctions(self::git($root, ['show', "{$base}:./{$path}"]), $absolute)) {
                        continue;
                    }

                    return new self($revision, $base, "{$path} changed");
                }

                // The base side counts too: a target dropped from covers() loses the tests that killed its mutants.
                foreach ([self::git($root, ['show', "{$base}:./{$path}"]), $isDeletedTest ? null : file_get_contents($absolute)] as $code) {
                    if (! is_string($code)) {
                        continue;
                    }

                    $covered = self::coveredBy($code);

                    if ($covered === null) {
                        return new self($revision, $base, "{$path} covers something other than class or namespace names");
                    }

                    if ($covered === [] && $isDeletedTest) {
                        return new self($revision, $base, "{$path} was deleted");
                    }

                    $targets = [...$targets, ...$covered];
                }

                $changedTests[$real] = true;

                continue;
            }

            if (str_ends_with($path, '.php') || in_array($path, self::PROJECT_FILES, true)) {
                return new self($revision, $base, "{$path} changed");
            }
        }

        foreach (array_unique($targets) as $target) {
            if (class_exists($target) || interface_exists($target) || trait_exists($target)) {
                $file = (string) new ReflectionClass($target)->getFileName();
                $files[realpath($file) ?: $file] = true;

                continue;
            }

            // mutates() also takes namespaces.
            foreach ($sources as $real => $file) {
                if (preg_match('/namespace\s+'.preg_quote($target, '/').'[\\\\\s;{]/', $file->getContents()) === 1) {
                    $files[$real] = true;
                }
            }
        }

        // Mutants the changed tests used to kill may live in any file they run, not only in what they cover.
        foreach ($coveredLines as $file => $lines) {
            if (isset($files[$file])) {
                continue;
            }

            foreach ($lines as $tests) {
                foreach ($tests as $test) {
                    if (isset($changedTests[self::testFile($test) ?? ''])) {
                        $files[$file] = true;

                        continue 3;
                    }
                }
            }
        }

        return new self($revision, $base, null, array_intersect_key($files, $sources));
    }

    public function includes(SplFileInfo $file): bool
    {
        return $this->everythingBecause !== null || isset($this->files[$file->getRealPath()]);
    }

    /**
     * @return array<int, string>
     */
    public function files(): array
    {
        return array_keys($this->files);
    }

    /**
     * @param  array<int, string>  $arguments
     */
    private static function git(string $root, array $arguments): ?string
    {
        $process = new Process(['git', ...$arguments], $root);
        $process->run();

        return $process->isSuccessful() ? $process->getOutput() : null;
    }

    /**
     * @return array<int, string>
     */
    private static function split(string $output): array
    {
        return array_values(array_filter(explode("\0", $output), fn (string $path): bool => $path !== ''));
    }

    private static function absolute(string $root, string $path): string
    {
        $path = str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : $root.DIRECTORY_SEPARATOR.$path;

        return rtrim(realpath($path) ?: $path, DIRECTORY_SEPARATOR);
    }

    /**
     * @param  array<int, string>  $roots
     */
    private static function within(string $path, array $roots): bool
    {
        return array_any($roots, fn (string $root): bool => $path === $root || str_starts_with($path, $root.DIRECTORY_SEPARATOR));
    }

    /**
     * Whether a support file such as tests/Pest.php only gained functions or imports, which no unchanged test can use.
     */
    private static function onlyAddsFunctions(?string $before, string $path): bool
    {
        $after = str_ends_with($path, '.php') && is_file($path) ? file_get_contents($path) : false;

        if ($before === null || $after === false) {
            return false;
        }

        try {
            [$before, $after] = array_map(fn (string $code): array => PhpParserFactory::make()->parse($code) ?? [], [$before, $after]);
        } catch (Error) {
            return false;
        }

        $printer = new Standard;
        $remaining = array_map(fn (Node $statement): string => $printer->prettyPrint([$statement]), $before);

        foreach ($after as $statement) {
            if ($remaining !== [] && $printer->prettyPrint([$statement]) === $remaining[0]) {
                array_shift($remaining);

                continue;
            }

            if (! $statement instanceof Function_ && ! $statement instanceof Use_ && ! $statement instanceof GroupUse && ! $statement instanceof Nop) {
                return false;
            }
        }

        return $remaining === [];
    }

    /**
     * The classes and namespaces a test file passes to covers() or mutates(), or null when an argument isn't a literal name.
     *
     * @return array<int, string>|null
     */
    private static function coveredBy(string $code): ?array
    {
        try {
            $statements = PhpParserFactory::make()->parse($code) ?? [];
        } catch (Error) {
            return null;
        }

        $statements = new NodeTraverser(new NameResolver)->traverse($statements);

        $calls = (new NodeFinder)->find($statements, fn (Node $node): bool => match (true) {
            $node instanceof FuncCall && $node->name instanceof Name => in_array(strtolower($node->name->getLast()), self::COVERING_CALLS, true),
            $node instanceof MethodCall && $node->name instanceof Identifier => in_array($node->name->toLowerString(), self::COVERING_CALLS, true),
            default => false,
        });

        $targets = [];

        foreach ($calls as $call) {
            assert($call instanceof FuncCall || $call instanceof MethodCall);

            if ($call->isFirstClassCallable()) {
                return null;
            }

            foreach ($call->getArgs() as $argument) {
                $names = $argument->unpack ? null : self::names($argument->value);

                if ($names === null) {
                    return null;
                }

                $targets = [...$targets, ...$names];
            }
        }

        return $targets;
    }

    /**
     * @return array<int, string>|null
     */
    private static function names(Expr $expression): ?array
    {
        if ($expression instanceof String_) {
            return [ltrim($expression->value, '\\')];
        }

        if ($expression instanceof ClassConstFetch && $expression->class instanceof Name && ! $expression->class->isSpecialClassName()
            && $expression->name instanceof Identifier && $expression->name->toLowerString() === 'class') {
            return [$expression->class->toString()];
        }

        if (! $expression instanceof Array_) {
            return null;
        }

        $names = [];

        foreach ($expression->items as $item) {
            $itemNames = $item->unpack ? null : self::names($item->value);

            if ($itemNames === null) {
                return null;
            }

            $names = [...$names, ...$itemNames];
        }

        return $names;
    }

    /**
     * The test file a coverage test id belongs to, when its generated test case is loaded.
     */
    private static function testFile(string $testId): ?string
    {
        $class = explode('::', $testId)[0];

        if (! array_key_exists($class, self::$testFilesByClass)) {
            $filename = class_exists($class, false) && property_exists($class, '__filename')
                ? new ReflectionClass($class)->getStaticPropertyValue('__filename')
                : null;

            self::$testFilesByClass[$class] = is_string($filename) ? (realpath($filename) ?: $filename) : null;
        }

        return self::$testFilesByClass[$class];
    }
}
