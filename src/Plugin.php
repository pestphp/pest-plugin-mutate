<?php

declare(strict_types=1);

namespace Pest\Mutate;

use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Plugins\Concerns\HandleArguments;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @final
 */
class Plugin implements HandlesArguments
{
    use HandleArguments;

    /**
     * Creates a new Plugin instance.
     */
    public function __construct(
        private readonly OutputInterface $output
    ) {
        // ..
    }

    /**
     * {@inheritdoc}
     */
    public function handleArguments(array $arguments): array
    {
        if (! $this->hasArgument('--mutate', $arguments)) {
            return $arguments;
        }

        $this->output->writeln('Running mutation tests...');

        $exitCode = 0;

        $this->exit($exitCode);
    }

    /**
     * Exits the process with the given code.
     */
    public function exit(int $code): never
    {
        exit($code);
    }
}
