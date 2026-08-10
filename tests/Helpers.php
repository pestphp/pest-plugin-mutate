<?php

declare(strict_types=1);

use Pest\Mutate\Factories\NodeTraverserFactory;
use Pest\Mutate\Support\PhpParserFactory;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Process\Process;

/**
 * Runs the mutation test suite of the given fixture, and returns its output
 * without the bits that change from machine to machine.
 */
function mutationTestOutput(string $fixture): string
{
    $process = new Process(
        ['php', 'vendor/bin/pest', 'tests/.tests/'.$fixture, '--mutate'],
        dirname(__DIR__),
        ['COLUMNS' => 80, 'XDEBUG_MODE' => 'coverage', 'PEST_PLUGIN_INTERNAL_TEST_SUITE' => 1],
    );

    $process->run();

    return (string) preg_replace([
        '#\x1b[[][^A-Za-z]*[A-Za-z]#', // colors
        '/ID: [0-9a-f]+/', // mutation ids, they are based on the absolute path of the file
        '/Duration: +.*/', // durations
    ], [
        '',
        'ID: xxx',
        'Duration: xxx',
    ], $process->getOutput());
}

function mutateCode(string $mutator, string $code): string
{
    $stmts = PhpParserFactory::make()->parse($code);

    $mutationCount = 0;

    $traverser = NodeTraverserFactory::create();
    $traverser->addVisitor(new class($mutator, function () use (&$mutationCount): void {
        $mutationCount++;
    }) extends NodeVisitorAbstract
    {
        public function __construct(
            private readonly string $mutator,
            private readonly Closure $incrementMutationCount,
        ) {}

        public function leaveNode(Node $node): mixed
        {
            if ($this->mutator::can($node)) {
                ($this->incrementMutationCount)();

                return $this->mutator::mutate($node);
            }

            return null;
        }
    });

    $newStmts = $traverser->traverse($stmts);

    if ($mutationCount === 0) {
        throw new Exception('No mutation performed');
    }

    $prettyPrinter = new Standard;

    return $prettyPrinter->prettyPrintFile($newStmts);
}
