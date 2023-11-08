<?php

declare(strict_types=1);

namespace Pest\Mutate\Repositories;

use Pest\Mutate\Mutators\Sets\DefaultSet;
use Pest\Mutate\Support\Configuration\CliConfiguration;
use Pest\Mutate\Support\Configuration\Configuration;
use Pest\Mutate\Support\Configuration\GlobalConfiguration;
use Pest\Mutate\Support\Configuration\TestConfiguration;

class ConfigurationRepository
{
    /**
     * @internal
     */
    final public const FAKE = 'fake-profile';

    public CliConfiguration $cliConfiguration;

    public TestConfiguration $testConfiguration;

    /**
     * @var array<string, GlobalConfiguration>
     */
    private array $globalConfigurations = [];

    /**
     * @var array<string, TestConfiguration>
     */
    private array $fakeTestConfigurations = [];

    private string $profile = 'default';

    public function __construct()
    {
        $this->cliConfiguration = new CliConfiguration();
        $this->testConfiguration = new TestConfiguration();
    }

    public function setProfile(string $profile): void
    {
        $this->profile = $profile;
    }

    public function globalConfiguration(string $name = null): GlobalConfiguration
    {
        $name ??= $this->profile;

        if (! isset($this->globalConfigurations[$name])) {
            $this->globalConfigurations[$name] = new GlobalConfiguration();
        }

        return $this->globalConfigurations[$name];
    }

    public function fakeTestConfiguration(string $name = null): TestConfiguration
    {
        $name ??= $this->profile;

        if (! isset($this->fakeTestConfigurations[$name])) {
            $this->fakeTestConfigurations[$name] = new TestConfiguration();
        }

        return $this->fakeTestConfigurations[$name];
    }

    public function mergedConfiguration(): Configuration
    {
        $config = [
            ...$this->globalConfiguration()->toArray(),
            ...$this->testConfiguration->toArray(),
            ...$this->cliConfiguration->toArray(),
        ];

        return new Configuration(
            coveredOnly: $config['covered_only'] ?? false,
            paths: $config['paths'] ?? ['src'],
            mutators: $config['mutators'] ?? DefaultSet::mutators(),
            classes: $config['classes'] ?? [],
            parallel: $config['parallel'] ?? false,
            minMSI: $config['min_msi'] ?? 0.0,
        );
    }
}
