<?php

namespace Pest\Mutate\Tester;

use Pest\Mutate\Contracts\MutationTestRunner as MutationTestRunnerContract;
use Pest\Support\Container;
use Symfony\Component\Console\Output\OutputInterface;

class MutationTestRunner implements MutationTestRunnerContract
{
    private ?string $enabledProfile = null;

    public static function fake(): void
    {
        Container::getInstance()->add(MutationTestRunnerContract::class, new MutationTestRunnerFake());
    }

    public function __construct(private readonly OutputInterface $output)
    {
    }

    public function enable(string $profile): void
    {
        $this->enabledProfile = $profile;
    }

    public function isEnabled(): bool
    {
        return $this->enabledProfile !== null;
    }

    public function run(): void
    {
        $this->output->writeln('Running mutation tests for profile: ' . $this->enabledProfile);

        exit(0);
    }
}
