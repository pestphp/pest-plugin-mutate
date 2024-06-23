<?php

declare(strict_types=1);

use Pest\Mutate\Factories\NodeTraverserFactory;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

function mutateCode(string $mutator, string $code): string
{
    $stmts = (new ParserFactory)->create(ParserFactory::PREFER_PHP7)->parse($code);

    $mutationCount = 0;

    $traverser = NodeTraverserFactory::create();
    $traverser->addVisitor(new class($mutator, function () use (&$mutationCount): void {
        $mutationCount++;
    }) extends NodeVisitorAbstract
    {
        public function __construct(
            private readonly string $mutator,
            private $incrementMutationCount,
        ) {}

        public function leaveNode(Node $node)
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

    $prettyPrinter = new Standard();

    return $prettyPrinter->prettyPrintFile($newStmts);
}
