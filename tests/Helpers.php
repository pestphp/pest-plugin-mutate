<?php

declare(strict_types=1);
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

function mutateCode(string $mutator, string $code): string
{
    $stmts = (new ParserFactory)->create(ParserFactory::PREFER_PHP7)->parse($code);

    $traverser = new NodeTraverser;
    $nameResolver = new NameResolver(null, ['replaceNodes' => false]);
    $traverser->addVisitor($nameResolver);
    $traverser->addVisitor(new ParentConnectingVisitor);
    $traverser->addVisitor(new class($mutator) extends NodeVisitorAbstract
    {
        public function __construct(private readonly string $mutator)
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
