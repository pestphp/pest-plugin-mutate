<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutation;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\SplFileInfo;

class MutationGenerator
{
    public static ?Node $lastMutatedNode = null;

    public static ?Node $lastMutatedNodeOriginal = null;

    private bool $mutated = false;

    private int $offset = 0;

    private Node $originalNode;

    private ?Node $modifiedNode = null;

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
     * @param  array<int, class-string<Mutator>>  $mutators
     * @param  array<int, int>  $linesToMutate
     * @return array<int, Mutation>
     */
    public function generate(SplFileInfo $file, array $mutators, array $linesToMutate = []): array
    {
        $mutations = [];

        $contents = $file->getContents();

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

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

            $this->offset = 0;

            while (true) {
                $this->mutated = false;

                $traverser = new NodeTraverser;
                $traverser->addVisitor(new NodeVisitor(
                    mutator: $mutator,
                    linesToMutate: $linesToMutate,
                    offset: $this->offset,
                    hasMutated: $this->hasMutated(...),
                    trackMutation: $this->trackMutation(...)
                ));

                $ast = $parser->parse($contents);

                $modifiedAst = $traverser->traverse($ast); // @phpstan-ignore-line

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

        return $mutations;
    }
}
