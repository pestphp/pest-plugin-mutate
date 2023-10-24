<?php

declare(strict_types=1);

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

function mutate(string $mutator, string $code): string
{
    $stmts = (new ParserFactory)->create(ParserFactory::PREFER_PHP7)->parse($code);

    $traverser = new NodeTraverser;
    $traverser->addVisitor(new class($mutator) extends NodeVisitorAbstract
    {
        public function __construct(private string $mutator)
        {
        }

        public function leaveNode(Node $node)
        {
            if ($this->mutator::can($node)) {
                return $this->mutator::mutate($node);
            }
        }
    });

    $newStmts = $traverser->traverse($stmts);

    $prettyPrinter = new Standard();

    return $prettyPrinter->prettyPrintFile($newStmts);
}
