<?php

declare(strict_types=1);

namespace Pest\Mutate\Support;

use Pest\Mutate\Contracts\Mutator;
use Pest\Mutate\Mutation;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\SplFileInfo;

class MutationGenerator
{
    public static ?int $lastMutatedNodeCount = null;

    public static ?Node $lastMutatedNode = null;

    public static ?Node $lastMutatedNodeOriginal = null;

    private bool $mutated = false;

    public function trackMutation(): void
    {
        $this->mutated = true;
    }

    public function hasMutated(): bool
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

            self::$lastMutatedNodeCount = null;

            while (true) {
                $this->mutated = false;
                self::$lastMutatedNode = null;
                self::$lastMutatedNodeOriginal = null;

                $traverser = new NodeTraverser;
                $traverser->addVisitor(new NodeVisitor($mutator, $this));

                try {
                    $ast = $parser->parse($contents);
                } catch (Error $error) {
                    dd("Parse error: {$error->getMessage()}");
                }

                $modifiedAst = $traverser->traverse($ast); // @phpstan-ignore-line

                if (! $this->hasMutated()) {
                    break;
                }

                $newMutations[] = new Mutation(
                    file: $file,
                    mutator: $mutator,
                    originalNode: self::$lastMutatedNodeOriginal, // @phpstan-ignore-line
                    mutatedNode: self::$lastMutatedNode,
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

        if ($linesToMutate !== []) {
            // TODO: this is probably wrong for multi line statements
            return array_filter($mutations, fn (Mutation $mutation): bool => in_array($mutation->originalNode->getStartLine(), $linesToMutate, true));
        }

        return $mutations;
    }
}
