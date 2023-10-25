<?php

declare(strict_types=1);

namespace Pest\Mutate\Tester;

use Pest\Support\Container;
use Symfony\Component\Console\Output\OutputInterface;

class MutationTester implements \Pest\Mutate\Contracts\MutationTester
{
    public static function fake(): void
    {
        Container::getInstance()->add(\Pest\Mutate\Contracts\MutationTester::class, new MutationTesterFake);
    }

    public function __construct(private readonly OutputInterface $output)
    {
    }

    public function run(): void
    {
        $this->output->writeln('Running mutation tests...');

        $exitCode = 0;

        exit($exitCode);
    }
}
