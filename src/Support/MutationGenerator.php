<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Factories\NodeTraverserFactory;
use Pest\Mutate\Mutation;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\SplFileInfo;

class MutationGenerator
{
    private bool $mutated;

    private int $offset;

    private Node $originalNode;

    private ?Node $modifiedNode = null;

    /**
     * @param  array<int, class-string<Mutator>>  $mutators
     * @param  array<int, int>  $linesToMutate
     * @param  array<int, string>  $classesToMutate
     * @return array<int, Mutation>
     */
    public function generate(
        SplFileInfo $file,
        array $mutators,
        array $linesToMutate = [],
        array $classesToMutate = [],
    ): array {
        $mutations = [];

        $contents = $file->getContents();

        if ($this->doesNotContainClassToMutate($contents, $classesToMutate)) {
            return $mutations;
        }

        $ignoreComments = [];
        foreach (explode(PHP_EOL, $contents) as $lineNumber => $line) {
            if (str_contains($line, '// @pest-mutate-ignore')) {
                $ignoreComments[] = ['line' => $lineNumber + 1, 'comment' => '// @pest-mutate-ignore'];
            }
        }

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $mutators = $this->filterMutators($mutators, $contents, $parser);

        //        $cache = MutationCache::instance();
        foreach ($mutators as $mutator) {
            //            if($cache->has($file, $mutator)){
            //                $mutations = [
            //                    ...$mutations,
            //                    ...$cache->get($file, $mutator),
            //                ];
            //                continue;
            //            }

            $newMutations = [];

            $this->offset = 0; // @pest-mutate-ignore: IntegerIncrement,IntegerDecrement

            while (true) {
                $this->mutated = false;

                $traverser = NodeTraverserFactory::create();
                $traverser->addVisitor(new NodeVisitor(
                    mutator: $mutator,
                    linesToMutate: $linesToMutate,
                    offset: $this->offset,
                    hasAlreadyMutated: $this->hasMutated(...),
                    trackMutation: $this->trackMutation(...),
                ));

                $modifiedAst = $traverser->traverse($parser->parse($contents)); // @phpstan-ignore-line

                if (! $this->hasMutated()) {
                    break;
                }

                $newMutations[] = new Mutation(
                    file: $file,
                    mutator: $mutator,
                    originalNode: $this->originalNode,
                    modifiedNode: $this->modifiedNode,
                    modifiedAst: $modifiedAst,
                );
            }

            //            $cache->put($file, $mutator, $newMutations);

            $mutations = [
                ...$mutations,
                ...$newMutations,
            ];
        }

        //        $cache->persist();

        // filter out mutations that are ignored
        $mutations = array_filter($mutations, function (Mutation $mutation) use ($ignoreComments): bool {
            foreach ($ignoreComments as $comment) {
                if ($comment['line'] === $mutation->originalNode->getStartLine()) {
                    return false;
                }
            }

            return true;
        });

        // sort mutations by line number
        usort($mutations, fn (Mutation $a, Mutation $b): int => $a->originalNode->getStartLine() <=> $b->originalNode->getStartLine());

        return $mutations;
    }

    private function trackMutation(int $nodeCount, Node $original, ?Node $modified): void
    {
        $this->mutated = true;
        $this->offset = $nodeCount;
        $this->originalNode = $original;
        $this->modifiedNode = $modified;
    }

    private function hasMutated(): bool
    {
        return $this->mutated;
    }

    /**
     * @param  array<int, string>  $classesToMutate
     */
    private function doesNotContainClassToMutate(string $contents, array $classesToMutate): bool
    {
        if ($classesToMutate === []) {
            return false;
        }

        foreach ($classesToMutate as $class) {
            $parts = explode('\\', $class);
            $class = array_pop($parts);
            $namespace = preg_quote(implode('\\', $parts));

            if (preg_match("/namespace\\s+${namespace}/", $contents) !== 1) {
                continue;
            }
            if (preg_match("/class\\s+${class}/", $contents) !== 1) {
                continue;
            }

            return false;
        }

        return true;
    }

    private function filterMutators(array $mutators, string $contents, \PhpParser\Parser $parser): array
    {
        $nodeTypes = [];

        $traverser = new NodeTraverser;
        $traverser->addVisitor(new class (function (string $nodeType) use (&$nodeTypes) {
            return $nodeTypes[] = $nodeType;
        }) extends NodeVisitorAbstract {
            public function __construct(private $callback){}

            public function enterNode(Node $node): void
            {
                ($this->callback)($node::class);
            }
        });
        $traverser->traverse($parser->parse($contents));

        $nodeTypes = array_unique($nodeTypes);

        $mutatorsToUse = [];
        foreach($nodeTypes as $nodeType){
            foreach(MutatorMap::get()[$nodeType] ?? [] as $mutator){
                $mutatorsToUse[] = $mutator;
            }
        }

        return array_intersect($mutators, $mutatorsToUse);
    }
}
